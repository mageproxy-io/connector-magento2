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
use Mageproxy\Connector\Api\Data\OptimizationBundleInterface;
use Mageproxy\Connector\Api\Data\OptimizationBundleSearchResultsInterface;

/**
 * Interface BundleRepositoryInterface
 * @package Mageproxy\Connector\Api
 */
interface OptimizationBundleRepositoryInterface
{
    /**
     * @param OptimizationBundleInterface $bundle
     * @return OptimizationBundleInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(OptimizationBundleInterface $bundle): OptimizationBundleInterface;

    /**
     * @param int $id
     * @return OptimizationBundleInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id): OptimizationBundleInterface;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Mageproxy\Connector\Api\Data\OptimizationBundleSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): OptimizationBundleSearchResultsInterface;

    /**
     * @param OptimizationBundleInterface $bundle
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(OptimizationBundleInterface $bundle): bool;

    /**
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $id): bool;
}
