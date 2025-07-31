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

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\Data\OptimizationInterfaceFactory;
use Mageproxy\Connector\Api\Data\OptimizationSearchResultsInterface;
use Mageproxy\Connector\Api\Data\OptimizationSearchResultsInterfaceFactory;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Model\ResourceModel\Optimization\CollectionFactory;

class OptimizationRepository implements OptimizationRepositoryInterface
{
    private ResourceModel\Optimization\CollectionFactory $collectionFactory;
    private CollectionProcessorInterface $collectionProcessor;
    private OptimizationSearchResultsInterfaceFactory $searchResultsFactory;
    private ResourceModel\Optimization $resource;
    private OptimizationInterfaceFactory $optimizationFactory;
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    public function __construct(
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        OptimizationSearchResultsInterfaceFactory $searchResultsFactory,
        \Mageproxy\Connector\Model\ResourceModel\Optimization $resource,
        OptimizationInterfaceFactory $optimizationFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->resource = $resource;
        $this->optimizationFactory = $optimizationFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): OptimizationSearchResultsInterface
    {
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $items = $collection->getItems();
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    public function getByRecordingId(int $recordingId): OptimizationSearchResultsInterface
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('recording_id', $recordingId)
            ->create();
        return $this->getList($searchCriteria);
    }

    /**
     * @inheritdoc
     */
    public function getById(int $id): OptimizationInterface
    {
        $optimization = $this->optimizationFactory->create();
        $this->resource->load($optimization, $id);
        if (!$optimization->getId()) {
            throw new NoSuchEntityException(
                __('Optimization with id "%1" does not exist.', $id)
            );
        }
        return $optimization;
    }

    /**
     * @inheritdoc
     */
    public function get(string $uuid): OptimizationInterface
    {
        $optimization = $this->optimizationFactory->create();
        $this->resource->load($optimization, $uuid, OptimizationInterface::UUID);
        if (!$optimization->getId()) {
            throw new NoSuchEntityException(
                __('Optimization with uuid "%1" does not exist.', $uuid)
            );
        }
        return $optimization;
    }

    /**
     * @inheritdoc
     */
    public function save(OptimizationInterface $optimization): OptimizationInterface
    {
        try {
            $this->resource->save($optimization);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
        return $optimization;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $id): bool
    {
        $optimization = $this->getById($id);
        return $this->delete($optimization);
    }

    /**
     * @inheritdoc
     */
    public function delete(OptimizationInterface $optimization): bool
    {
        $this->resource->delete($optimization);
        return true;
    }
}
