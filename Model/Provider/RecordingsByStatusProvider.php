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
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Model\ProviderInterface;

class RecordingsByStatusProvider implements ProviderInterface
{
    private RecordingRepositoryInterface $recordingRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private FilterBuilder $filterBuilder;
    private SortOrderBuilder $sortOrderBuilder;
    private array $statuses;

    public function __construct(
        RecordingRepositoryInterface $recordingRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        SortOrderBuilder $sortOrderBuilder,
        array $statuses = []
    ) {
        $this->recordingRepository = $recordingRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->statuses = $statuses;
    }

    public function getItems(): array
    {
        $statusFilter = null;
        if ($this->statuses) {
            $statusFilter = $this->filterBuilder
                ->setField(RecordingInterface::STATUS)
                ->setValue($this->statuses)
                ->setConditionType('IN')
                ->create();
        }
        $sortByScheduledAt = $this->sortOrderBuilder
            ->setField(RecordingInterface::SCHEDULED_AT)
            ->setDirection(SortOrder::SORT_ASC)
            ->create();

        if ($statusFilter) {
            $this->searchCriteriaBuilder->addFilters([$statusFilter]);
        }

        $searchCriteria = $this->searchCriteriaBuilder->addSortOrder($sortByScheduledAt)
                ->create();

        return $this->recordingRepository->getList(
            $searchCriteria
        )->getItems();
    }
}
