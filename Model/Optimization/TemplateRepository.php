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

use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageproxy\Connector\Api\Data\OptimizationTemplateInterface;
use Mageproxy\Connector\Api\Data\OptimizationTemplateInterfaceFactory;
use Mageproxy\Connector\Api\Data\OptimizationTemplateSearchResultsInterface;
use Mageproxy\Connector\Api\Data\OptimizationTemplateSearchResultsInterfaceFactory;
use Mageproxy\Connector\Api\OptimizationTemplateRepositoryInterface;
use Mageproxy\Connector\Model\ResourceModel\Optimization\Template as TemplateResource;
use Mageproxy\Connector\Model\ResourceModel\Optimization\Template\CollectionFactory;

class TemplateRepository implements OptimizationTemplateRepositoryInterface
{
    private CollectionFactory $collectionFactory;
    private CollectionProcessor $collectionProcessor;
    private OptimizationTemplateSearchResultsInterfaceFactory $searchResultsFactory;
    private TemplateResource $resource;
    private OptimizationTemplateInterfaceFactory $templateFactory;

    public function __construct(
        CollectionFactory $collectionFactory,
        CollectionProcessor $collectionProcessor,
        OptimizationTemplateSearchResultsInterfaceFactory $searchResultsFactory,
        TemplateResource $resource,
        OptimizationTemplateInterfaceFactory $templateFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->resource = $resource;
        $this->templateFactory = $templateFactory;
    }
    /**
     * @inheritdoc
     */
    public function save(OptimizationTemplateInterface $template): OptimizationTemplateInterface
    {
        try {
            $this->resource->save($template);
        } catch (Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
        return $template;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $id): OptimizationTemplateInterface
    {
        $template = $this->templateFactory->create();
        $this->resource->load($template, $id);
        if (!$template->getId()) {
            throw new NoSuchEntityException(__('Template with id "%1" does not exist.', $id));
        }
        return $template;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): OptimizationTemplateSearchResultsInterface
    {
        $collection = $this->collectionFactory->create();
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
    public function delete(OptimizationTemplateInterface $template): bool
    {
        try {
            $this->resource->delete($template);
        } catch (Exception $e) {
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
