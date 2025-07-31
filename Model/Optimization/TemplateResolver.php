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

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Store\Model\Store;
use Mageproxy\Connector\Api\Data\OptimizationTemplateInterface;
use Mageproxy\Connector\Api\Data\OptimizationTemplateInterfaceFactory;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\OptimizationTemplateRepositoryInterface;
use Mageproxy\Connector\Model\Config;

/**
 * Given a recording, the resolver will return the optimization template taking into account priority
 */
class TemplateResolver
{
    /**
     * @var \Mageproxy\Connector\Api\OptimizationTemplateRepositoryInterface
     */
    private OptimizationTemplateRepositoryInterface $optimizationTemplateRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\SortOrderBuilder
     */
    private SortOrderBuilder $sortOrderBuilder;

    /**
     * @var \Mageproxy\Connector\Model\Config
     */
    private Config $config;
    /**
     * @var \Mageproxy\Connector\Api\Data\OptimizationTemplateInterfaceFactory
     */
    private OptimizationTemplateInterfaceFactory $optimizationTemplateFactory;

    public function __construct(
        OptimizationTemplateRepositoryInterface $optimizationTemplateRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        OptimizationTemplateInterfaceFactory $optimizationTemplateFactory,
        Config $config
    ) {
        $this->optimizationTemplateRepository = $optimizationTemplateRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->config = $config;
        $this->optimizationTemplateFactory = $optimizationTemplateFactory;
    }

    public function resolve(RecordingInterface $recording): OptimizationTemplateInterface
    {
        $storeId = $recording->getStoreId();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(
                'store_id',
                [ Store::DEFAULT_STORE_ID, $storeId ],
                'in'
            )->addSortOrder(
                 $this->sortOrderBuilder->setField('store_id')->setDescendingDirection()->create()
            )->create();

        $result = $this->optimizationTemplateRepository->getList($searchCriteria);

        if ($result->getTotalCount() === 0) {
            return $this->optimizationTemplateFactory->create(['data' => [
                'minify_js' => $this->config->getMinifyJs($storeId ?: null),
                'minify_html' => $this->config->getMinifyHtml($storeId ?: null)
            ]]);
        }

        $items = $result->getItems();
        /** @var \Mageproxy\Connector\Api\Data\OptimizationTemplateInterface $item */
        $item = array_shift($items);
        return $item;
    }
}
