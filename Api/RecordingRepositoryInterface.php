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
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\Data\RecordingSearchResultsInterface;

interface RecordingRepositoryInterface
{
    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Mageproxy\Connector\Api\Data\RecordingSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): RecordingSearchResultsInterface;

    /**
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $recording
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(RecordingInterface $recording): RecordingInterface;

    /**
     * Load recording by uuid
     *
     * @param string $uuid
     * @return \Mageproxy\Connector\Api\Data\RecordingInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(string $uuid): RecordingInterface;

    /**
     * @param int $id
     * @return \Mageproxy\Connector\Api\Data\RecordingInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id): RecordingInterface;

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById(int $id): bool;

    /**
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $recording
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(RecordingInterface $recording): bool;
}
