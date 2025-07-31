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
use Mageproxy\Connector\Api\RecordingRepositoryInterface;

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$recordingRepository = $objectManager->create(RecordingRepositoryInterface::class);
$recording = $recordingRepository->get('finished');
try {
    $recordingRepository->delete($recording);
} catch (CouldNotDeleteException $e) {
}

$optimizationRepository = $objectManager->create(OptimizationRepositoryInterface::class);
$searchCriteriaBuilder = $objectManager->create(SearchCriteriaBuilder::class);
$optimizations = $optimizationRepository->getList($searchCriteriaBuilder->create())->getItems();
foreach ($optimizations as $optimization) {
    try {
        $recordingRepository->delete($optimization);
    } catch (CouldNotDeleteException $e) {
    }
}
