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

use Mageproxy\Connector\Api\Data\OptimizationTemplateInterface;

interface OptimizationTemplateRepositoryInterface
{
    /**
     * @param \Mageproxy\Connector\Api\Data\OptimizationTemplateInterface $template
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Mageproxy\Connector\Api\Data\OptimizationTemplateInterface $template): OptimizationTemplateInterface;

    /**
     * @param int $id
     * @return \Mageproxy\Connector\Api\Data\OptimizationTemplateInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id): \Mageproxy\Connector\Api\Data\OptimizationTemplateInterface;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Mageproxy\Connector\Api\Data\OptimizationTemplateSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria): \Mageproxy\Connector\Api\Data\OptimizationTemplateSearchResultsInterface;

    /**
     * @param \Mageproxy\Connector\Api\Data\OptimizationTemplateInterface $template
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return void
     */
    public function delete(\Mageproxy\Connector\Api\Data\OptimizationTemplateInterface $template): bool;

    /**
     * @param int $id
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return bool
     */
    public function deleteById(int $id): bool;
}
