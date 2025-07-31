<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\Data\OptimizationSearchResultsInterface;

interface OptimizationRepositoryInterface
{
    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Mageproxy\Connector\Api\Data\OptimizationSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): OptimizationSearchResultsInterface;

    /**
     * @param int $recordingId
     * @return \Mageproxy\Connector\Api\Data\OptimizationSearchResultsInterface
     */
    public function getByRecordingId(int $recordingId): OptimizationSearchResultsInterface;

    /**
     * @param int $id
     * @return \Mageproxy\Connector\Api\Data\OptimizationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id): OptimizationInterface;

    /**
     * @param string $uuid
     * @return \Mageproxy\Connector\Api\Data\OptimizationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(string $uuid): OptimizationInterface;

    /**
     * @param \Mageproxy\Connector\Api\Data\OptimizationInterface $optimization
     * @return \Mageproxy\Connector\Api\Data\OptimizationInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(OptimizationInterface $optimization): OptimizationInterface;

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $id): bool;

    /**
     * @param \Mageproxy\Connector\Api\Data\OptimizationInterface $optimization
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(OptimizationInterface $optimization): bool;
}
