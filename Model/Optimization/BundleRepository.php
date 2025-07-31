<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\Optimization;

use Magento\Framework\Api\SearchCriteria\CollectionProcessor;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageproxy\Connector\Api\Data\OptimizationBundleInterface;
use Mageproxy\Connector\Api\Data\OptimizationBundleSearchResultsInterface;
use Mageproxy\Connector\Api\Data\OptimizationBundleSearchResultsInterfaceFactory;
use Mageproxy\Connector\Api\OptimizationBundleRepositoryInterface;
use Mageproxy\Connector\Model\ResourceModel\Optimization\Bundle as BundleResource;
use Mageproxy\Connector\Model\ResourceModel\Optimization\Bundle\CollectionFactory as BundleCollectionFactory;

class BundleRepository implements OptimizationBundleRepositoryInterface
{
    private BundleResource $resource;
    private BundleFactory $bundleFactory;
    private BundleCollectionFactory $bundleCollectionFactory;
    private CollectionProcessor $collectionProcessor;
    private OptimizationBundleSearchResultsInterfaceFactory $searchResultsFactory;

    public function __construct(
        BundleResource $resource,
        BundleFactory $bundleFactory,
        BundleCollectionFactory $bundleCollectionFactory,
        CollectionProcessor $collectionProcessor,
        OptimizationBundleSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->bundleFactory = $bundleFactory;
        $this->bundleCollectionFactory = $bundleCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(OptimizationBundleInterface $bundle): OptimizationBundleInterface
    {
        try {
            $this->resource->save($bundle);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $bundle;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $id): OptimizationBundleInterface
    {
        $bundle = $this->bundleFactory->create();
        $this->resource->load($bundle, $id);

        if (!$bundle->getId()) {
            throw new NoSuchEntityException(__('Bundle with id "%1" does not exist.', $id));
        }

        return $bundle;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): OptimizationBundleSearchResultsInterface
    {
        $collection = $this->bundleCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function delete(OptimizationBundleInterface $bundle): bool
    {
        try {
            $this->resource->delete($bundle);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $id): bool
    {
        return $this->delete($this->getById($id));
    }
}
