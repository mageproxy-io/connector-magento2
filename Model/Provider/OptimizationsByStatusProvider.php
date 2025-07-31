<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\Provider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Model\ProviderInterface;

class OptimizationsByStatusProvider implements ProviderInterface
{
    /**
     * @var \Mageproxy\Connector\Api\OptimizationRepositoryInterface
     */
    private OptimizationRepositoryInterface $optimizationRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private FilterBuilder $filterBuilder;

    /**
     * @var \Magento\Framework\Api\SortOrderBuilder
     */
    private SortOrderBuilder $sortOrderBuilder;

    public function __construct(
        OptimizationRepositoryInterface $optimizationRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->optimizationRepository = $optimizationRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        $requestedFilter = $this->filterBuilder
            ->setField(OptimizationInterface::STATUS)
            ->setValue(OptimizationInterface::STATUS_REQUESTED)
            ->create();

        $publishedFilter = $this->filterBuilder
            ->setField(OptimizationInterface::STATUS)
            ->setValue(OptimizationInterface::STATUS_PUBLISHED)
            ->create();

        $sortById = $this->sortOrderBuilder
            ->setField(OptimizationInterface::ID)
            ->setDirection(SortOrder::SORT_ASC)
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilters([$requestedFilter, /* OR */ $publishedFilter])
            ->addSortOrder($sortById)
            ->create();

        $result = $this->optimizationRepository->getList(
            $searchCriteria
        );

        return $result->getItems();
    }
}
