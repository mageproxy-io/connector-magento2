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
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;

class SearchCriteriaProvider
{
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private FilterBuilder $filterBuilder;
    private SortOrderBuilder $sortOrderBuilder;
    private ?array $statuses = null;
    private ?string $sortByField = null;
    private ?string $sortByDirection = null;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        SortOrderBuilder $sortOrderBuilder,
        array $statuses = [],
        string $sortByField = null,
        string $sortByDirection = SortOrder::SORT_ASC
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->statuses = $statuses;
        $this->sortByField = $sortByField;
        $this->sortByDirection = $sortByDirection;
    }

    public function getSearchCriteria(): SearchCriteria
    {
        if ($this->statuses) {
            $statusFilter = $this->filterBuilder
                ->setField('status')
                ->setValue($this->statuses)
                ->setConditionType('in')
                ->create();
            $this->searchCriteriaBuilder->addFilters([$statusFilter]);
        }
        if (isset($this->sortByField)) {
            $sortBy = $this->sortOrderBuilder
                ->setField($this->sortByField)
                ->setDirection($this->sortByDirection)
                ->create();
            $this->searchCriteriaBuilder->addSortOrder($sortBy);
        }
        return $this->searchCriteriaBuilder->create();
    }
}
