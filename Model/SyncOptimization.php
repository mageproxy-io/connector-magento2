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
use Mageproxy\Connector\Api\Data\OptimizationBundleInterfaceFactory;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\OptimizationBundleRepositoryInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Model\ApiClient\GetOptimizationInterface;
use Mageproxy\Connector\Model\ApiClient\GetOptimizationResponseInterface;

class SyncOptimization
{
    private GetOptimizationInterface $optimizationApiClient;
    private OptimizationRepositoryInterface $optimizationRepository;
    private OptimizationBundleInterfaceFactory $bundleFactory;
    private OptimizationBundleRepositoryInterface $bundleRepository;
    private OptimizationBundleRepositoryInterface $optimizationBundleRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    public function __construct(
        GetOptimizationInterface $optimizationApiClient,
        OptimizationRepositoryInterface $optimizationRepository,
        OptimizationBundleInterfaceFactory $bundleFactory,
        OptimizationBundleRepositoryInterface $bundleRepository,
        OptimizationBundleRepositoryInterface $optimizationBundleRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->optimizationApiClient = $optimizationApiClient;
        $this->optimizationRepository = $optimizationRepository;
        $this->bundleFactory = $bundleFactory;
        $this->bundleRepository = $bundleRepository;
        $this->optimizationBundleRepository = $optimizationBundleRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function execute(OptimizationInterface $optimization): void
    {
        $this->updateFromApi($optimization);
    }

    private function updateFromApi(OptimizationInterface $optimization): void
    {
        $response = $this->optimizationApiClient->execute($optimization->getUuid());
        if (!$response) {
            return;
        }

        $mappedStatus = $this->mapStatus($response);
        $optimization->setStatus($mappedStatus);
        $optimization->setErrorMessage($response->getError());
        $this->optimizationRepository->save($optimization);

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(
                'optimization_id', $optimization->getId()
            )->create();

        if ($this->optimizationBundleRepository->getList($searchCriteria)->getTotalCount()) {
            // bundles are a one-off thing, they'll never be updated on a optimization
            return;
        }

        if ($response->getBundles()) {
            foreach ($response->getBundles() as $bundle) {
                $bundleModel = $this->bundleFactory->create();
                $bundleModel->setUrl($bundle->getUrl());
                $bundleModel->setSriHash($bundle->getSriHash());
                $bundleModel->setRawSize($bundle->getRawSize());
                $bundleModel->setCompressedSize($bundle->getComSize());
                $bundleModel->setMinifiedSize($bundle->getMinSize());
                $bundleModel->setOptimization($optimization);
                $this->bundleRepository->save($bundleModel);
            }
        }
    }

    /**
     * Map api status label to extension numerical status codes
     *
     * @param \Mageproxy\Connector\Model\ApiClient\GetOptimizationResponseInterface $response
     * @return int
     */
    private function mapStatus(GetOptimizationResponseInterface $response): int
    {
        $status = $response->getStatus();
        switch ($status) {
            case GetOptimizationResponseInterface::STATUS_PUBLISHED:
                return OptimizationInterface::STATUS_PUBLISHED;
            case GetOptimizationResponseInterface::STATUS_READY:
                return OptimizationInterface::STATUS_READY;
            case GetOptimizationResponseInterface::STATUS_DEPLOYED:
                return OptimizationInterface::STATUS_DEPLOYED;
            case GetOptimizationResponseInterface::STATUS_ERROR:
                return OptimizationInterface::STATUS_FAILED;
            default:
                return OptimizationInterface::STATUS_REQUESTED;
        }
    }
}
