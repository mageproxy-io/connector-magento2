<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$recordingRepository = $objectManager->create(OptimizationRepositoryInterface::class);
$searchCriteriaBuilder = $objectManager->create(SearchCriteriaBuilder::class);
$recordings = $recordingRepository->getList($searchCriteriaBuilder->create())->getItems();
foreach ($recordings as $recording) {
    try {
        $recordingRepository->delete($recording);
    } catch (CouldNotDeleteException $e) {
    }
}
