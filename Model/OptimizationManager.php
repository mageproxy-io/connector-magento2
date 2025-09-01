<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DataObject\Copy as CopyObject;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\Data\OptimizationInterfaceFactory;
use Mageproxy\Connector\Api\Data\OptimizationTemplateInterface;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\OptimizationManagerInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Api\RecordingManagerInterface;
use Mageproxy\Connector\Model\ApiClient\DeleteOptimizationInterface;
use Mageproxy\Connector\Model\ApiClient\DistributionResolver;
use Mageproxy\Connector\Model\ApiClient\PostOptimizeRecordingInterface;
use Mageproxy\Connector\Model\ApiClient\PostOptimizeRecordingRequestInterfaceFactory;

class OptimizationManager implements OptimizationManagerInterface
{
    private StoreManagerInterface $storeManager;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private Config $config;
    private OptimizationRepositoryInterface $optimizationRepository;
    private DistributionResolver $magentoDistributionResolver;
    private ApiClient\PostOptimizeRecordingRequestInterfaceFactory $requestInterfaceFactory;
    private ApiClient\PostOptimizeRecordingInterface $postOptimizeRecording;
    private OptimizationInterfaceFactory $optimizationInterfaceFactory;
    private PurgeFullPageCache $purgeFullPageCache;
    private DateTime $dateTimeFmt;
    private CopyObject $objectCopyService;
    private RecordingManagerInterface $recordingManager;
    private DeleteOptimizationInterface $deleteOptimizationApi;

    public function __construct(
        StoreManagerInterface $storeManager,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OptimizationRepositoryInterface $optimizationRepository,
        DistributionResolver $magentoDistributionResolver,
        PostOptimizeRecordingRequestInterfaceFactory $requestInterfaceFactory,
        PostOptimizeRecordingInterface $postOptimizeRecording,
        OptimizationInterfaceFactory $optimizationInterfaceFactory,
        Config $config,
        PurgeFullPageCache $purgeFullPageCache,
        DateTime $dateTimeFmt,
        CopyObject $objectCopyService,
        RecordingManagerInterface $recordingManager,
        DeleteOptimizationInterface $deleteOptimizationApi
    ) {
        $this->storeManager = $storeManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->config = $config;
        $this->optimizationRepository = $optimizationRepository;
        $this->magentoDistributionResolver = $magentoDistributionResolver;
        $this->requestInterfaceFactory = $requestInterfaceFactory;
        $this->postOptimizeRecording = $postOptimizeRecording;
        $this->optimizationInterfaceFactory = $optimizationInterfaceFactory;
        $this->purgeFullPageCache = $purgeFullPageCache;
        $this->dateTimeFmt = $dateTimeFmt;
        $this->objectCopyService = $objectCopyService;
        $this->recordingManager = $recordingManager;
        $this->deleteOptimizationApi = $deleteOptimizationApi;
    }

    public function deploy(OptimizationInterface $optimization): void
    {
        if ($optimization->getStatus() === OptimizationInterface::STATUS_DEPLOYED) {
            return;
        }
        if ($optimization->getStatus() !== OptimizationInterface::STATUS_READY) {
            throw new LocalizedException(__('Optimization is not ready for deployment.'));
        }
        if ($this->deploymentInProgress($optimization->getStoreId())) {
            throw new LocalizedException(
                __('An optimization is already deployed, first revert it before deploying another optimization.')
            );
        }
        $optimization->setStatus(OptimizationInterface::STATUS_DEPLOYED);
        $optimization->setDeployedAt($this->dateTimeFmt->formatDate(true));
        try {
            $this->optimizationRepository->save($optimization);
            $this->purgeFullPageCache->shouldPurge();
        } catch (CouldNotSaveException $e) {
            throw new LocalizedException(__('Could not deploy optimization.'));
        }
    }

    /**
     * @inheritdoc
     */
    public function revert(OptimizationInterface $optimization, int $afterStatus = OptimizationInterface::STATUS_READY): void
    {
        if ($optimization->getStatus() !== OptimizationInterface::STATUS_DEPLOYED) {
            throw new LocalizedException(__('Optimization is not deployed'));
        }
        $optimization->setStatus($afterStatus);
        try {
            $this->optimizationRepository->save($optimization);
            $this->purgeFullPageCache->shouldPurge();
            if ($afterStatus === OptimizationInterface::STATUS_FINISHED) {
                $this->finish($optimization);
            }
        } catch (CouldNotSaveException|LocalizedException $e) {
            throw new LocalizedException(__('Could not revert optimization'));
        }
    }

    public function finish(OptimizationInterface $optimization): void
    {
        if ($optimization->getStatus() !== OptimizationInterface::STATUS_FINISHED) {
            $optimization->setStatus(OptimizationInterface::STATUS_FINISHED);
            try {
                $this->optimizationRepository->save($optimization);
            } catch (\Exception $e) {
                throw new LocalizedException(__('Could not finish optimization.'));
            }
        }
        $this->deleteOptimizationApi->execute($optimization->getUuid());
    }

    /**
     * @inheritdoc
     */
    public function deploymentInProgress(?int $storeId = null): bool
    {
        if (!$this->config->getIsEnabled()) {
            return false;
        }
        return $this->getDeployedOptimization($storeId) !== null;
    }

    /**
     * @inheritdoc
     */
    public function getDeployedOptimization(?int $storeId = null): ?OptimizationInterface
    {
        $this->searchCriteriaBuilder->addFilter(
            'store_id',
            $storeId ?? $this->getCurrentStoreId()
        );
        $this->searchCriteriaBuilder->addFilter(
            'status',
            OptimizationInterface::STATUS_DEPLOYED
        );
        $this->searchCriteriaBuilder->setPageSize(1);
        $this->searchCriteriaBuilder->setCurrentPage(1);
        $result = $this->optimizationRepository->getList(
            $this->searchCriteriaBuilder->create()
        );
        if ($result->getTotalCount() === 0) {
            return null;
        }
        $items = $result->getItems();
        return array_shift($items);
    }

    /**
     * @inheritdoc
     */
    public function request(
        RecordingInterface $recording,
        string $requestedBy,
        OptimizationTemplateInterface $template
    ): OptimizationInterface {

        // Get the latest stats for this recording from the API
        $this->recordingManager->populateFromLatestSnapshot($recording);

        if (!$this->recordingManager->hasDependencies($recording)) {
            throw new LocalizedException(__('Recording has no dependencies. Optimization is not possible.'));
        }

        $magentoDistribution = $this->magentoDistributionResolver->resolve();

        $request = $this->requestInterfaceFactory->create();

        $this->objectCopyService->copyFieldsetToTarget(
            'mageproxy_optimization_template',
            'to_request',
            $template,
            $request
        );

        $request->setDistribution($magentoDistribution);
        $request->setStoreViewCode($this->storeManager->getStore($recording->getStoreId())->getCode());

        $result = $this->postOptimizeRecording->execute($recording->getUuid(), $request);
        if (!$result || !$result->getId()) {
            throw new LocalizedException(
                __('Could not create optimization. Please contact support.')
            );
        }

        /** @var \Mageproxy\Connector\Model\Optimization $optimization */
        $optimization = $this->optimizationInterfaceFactory->create();

        $this->objectCopyService->copyFieldsetToTarget(
            'mageproxy_optimization_template',
            'to_optimization',
            $template,
            $optimization
        );

        $optimization->setRecordingId((int) $recording->getId());
        $optimization->setRecording($recording);
        $optimization->setUuid($result->getId());
        $optimization->setDepsCount($recording->getDepsCnt());
        $optimization->setHdlsCount($recording->getHdlsCnt());
        $optimization->setRequestedBy($requestedBy);
        $optimization->setRecordingChecksum($recording->getChecksum());

        $this->optimizationRepository->save($optimization);

        // Finish all relevant previous optimizations
        $optsToFinish = $this->optimizationRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter('recording_id', $recording->getId())
                ->addFilter('optimization_id', $optimization->getId(), 'neq')
                ->addFilter('status', OptimizationInterface::STATUS_FINISHED, 'neq') // Ignore already finished optimizations
                ->addFilter('status', OptimizationInterface::STATUS_DEPLOYED, 'neq') // Ignore deployed optimizations!
                ->create()
        )->getItems();
        foreach ($optsToFinish as $optToFinish) {
            $this->finish($optToFinish);
        }

        return $optimization;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCurrentStoreId(): int
    {
        return (int) $this->storeManager->getStore()->getId();
    }
}
