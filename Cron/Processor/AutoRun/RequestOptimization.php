<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Cron\Processor\AutoRun;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\OptimizationManagerInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterfaceFactory;
use Mageproxy\Connector\Api\RecordingManagerInterface;
use Mageproxy\Connector\Cron\AbstractProcessor;
use Mageproxy\Connector\Model\Config;
use Mageproxy\Connector\Model\Optimization\TemplateResolver;
use Mageproxy\Connector\Model\ProviderInterface;
use Mageproxy\Connector\Model\System\Config\Source\AutoRunType;

class RequestOptimization extends AbstractProcessor
{
    private OptimizationManagerInterface $optimizationManager;
    private TemplateResolver $templateResolver;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private OptimizationRepositoryInterfaceFactory $optimizationRepositoryFactory;
    private SortOrderBuilder $sortOrderBuilder;
    private Config $config;
    private RecordingManagerInterface $recordingManager;

    public function __construct(
        ProviderInterface $provider,
        OptimizationManagerInterface $optimizationManager,
        TemplateResolver $templateResolver,
        OptimizationRepositoryInterfaceFactory $optimizationRepositoryFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        Config $config,
        RecordingManagerInterface $recordingManager
    ) {
        parent::__construct($provider);
        $this->optimizationManager = $optimizationManager;
        $this->templateResolver = $templateResolver;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->optimizationRepositoryFactory = $optimizationRepositoryFactory;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->config = $config;
        $this->recordingManager = $recordingManager;
    }

    /**
     * @param $entity
     * @return void
     */
    public function process($entity): void
    {
        /** @var \Mageproxy\Connector\Api\Data\RecordingInterface $recording */
        $recording = $entity;

        $this->recordingManager->populateFromLatestSnapshot($recording);

        if (!$this->canRequest($recording)) {
            return;
        }

        if ($recording->getAutoRunType() === AutoRunType::SCHEDULED
            && $recording->getStatus() !== RecordingInterface::STATUS_STOPPED
        ) {
            // never create optimization for scheduled recordings that are not stopped
            return;
        }

        $lastOptimization = $this->getLastOptimization($recording);

        if ($lastOptimization) {
            try {
                if ($recording->getChecksum() !== null &&
                    $recording->getChecksum() === $lastOptimization->getRecordingChecksum()
                ) {
                    // If the recording snapshot is the same as the last optimization, no need to request a new
                    // optimization
                    return;
                }
            } catch (\Exception $e) {
                return;
            }
        }

        if ($recording->getStatus() === RecordingInterface::STATUS_STOPPED
            && !empty($recording->getRecordSchedule())
        ) {
            $this->processScheduled($recording, $lastOptimization);
        }

        if ($recording->getStatus() === RecordingInterface::STATUS_RUNNING
            && empty($recording->getRecordSchedule())
        ) {
            $this->processContinuous($recording, $lastOptimization);
        }
    }

    /**
     * Handle optimization request if recording is running in auto scheduled
     *
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $recording
     * @param \Mageproxy\Connector\Api\Data\OptimizationInterface $lastOptimization
     * @return void
     */
    private function processScheduled(RecordingInterface $recording, ?OptimizationInterface $lastOptimization = null): void
    {
        $requestedAt = $lastOptimization ? $lastOptimization->getRequestedAt() : $recording->getStartedAt();
        $finishedAt = $recording->getFinishedAt();
        if (strtotime($requestedAt) < strtotime($finishedAt)) {
            $this->request($recording);
        }
    }

    /**
     * Handle optimization request when recording is running auto continuous
     *
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $recording
     * @param \Mageproxy\Connector\Api\Data\OptimizationInterface $lastOptimization
     * @return void
     */
    private function processContinuous(RecordingInterface $recording, ?OptimizationInterface $lastOptimization = null)
    {
        $requestedAt = $lastOptimization ? $lastOptimization->getRequestedAt() : $recording->getStartedAt();
        $frequency = $this->config->getAutoRunOptimizationFrequency($recording->getStoreId());
        if ((strtotime($requestedAt) + $frequency) < time()) {
            $this->request($recording);
        }
    }

    /**
     * Request a new optimization from the API
     *
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $recording
     * @return void
     */
    private function request(RecordingInterface $recording)
    {
        $this->optimizationManager->request(
            $recording,
            OptimizationInterface::REQUESTED_BY_CRON,
            $this->templateResolver->resolve($recording)
        );
    }

    /**
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $recording
     * @return bool
     */
    private function canRequest(RecordingInterface $recording): bool
    {
        if ($recording->getInitiator() === RecordingInterface::INITIATOR_USER) {
            return false; // User initiated, handled manually
        }
        if (empty($recording->getDepsCnt())) {
            return false;
        }
        return true;
    }

    /**
     * Get last requested optimization or null (if non requested)
     *
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $recording
     * @return \Mageproxy\Connector\Api\Data\OptimizationInterface|null
     */
    private function getLastOptimization(RecordingInterface $recording): ?OptimizationInterface
    {
        $sortOrder = $this->sortOrderBuilder
            ->setField(OptimizationInterface::REQUESTED_AT)
            ->setDescendingDirection()
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(
                'recording_id', $recording->getId()
            )->setPageSize(
                1
            )->setCurrentPage(
                1
            )->addSortOrder(
                $sortOrder
            )->create();

        $repository = $this->optimizationRepositoryFactory->create();

        $items = $repository->getList($searchCriteria)->getItems();
        return !empty($items) ? array_shift($items) : null;
    }
}
