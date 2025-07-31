<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\Data\OptimizationInterfaceFactory;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/**
 * Explicitly getting the first recording instance through the collection
 * since we want to be able to inject different status recordings
 * via the @magentoDataFixture annotation
 */
$collection = $objectManager->create(\Mageproxy\Connector\Model\ResourceModel\Recording\Collection::class);
$recording = $collection->getFirstItem();

$factory = $objectManager->get(OptimizationInterfaceFactory::class);
$repository = $objectManager->get(OptimizationRepositoryInterface::class);
/** @var \Mageproxy\Connector\Model\Optimization $optimization */
$optimization = $factory->create();
$optimization->setRecording($recording);
$optimization->setMinifyHtml(true);
$optimization->setMinifyJs(true);
$optimization->setUuid('requested');
$optimization->setStatus(OptimizationInterface::STATUS_REQUESTED);
$optimization->setRecordingChecksum('9cdeb9d0f43cec81cc80654973e56c94904aeff495fe008c608d57f3d99d59fc');
$repository->save($optimization);
