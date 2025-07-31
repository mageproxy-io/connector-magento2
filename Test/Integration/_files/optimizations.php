<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

use Magento\Store\Model\Store;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Model\Optimization\Source\Status;
use Mageproxy\Connector\Model\ResourceModel\Optimization\Collection;

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

Resolver::getInstance()->requireDataFixture('Mageproxy_Connector::Test/Integration/_files/recording_finished.php');

$optimizationCollection = $objectManager->create(Collection::class);
$recordingRepository = $objectManager->get(RecordingRepositoryInterface::class);
$recording = $recordingRepository->get('finished');
$sourceModel = $objectManager->create(Status::class);

for ($i = 0; $i < 6; $i++) {
    /** @var \Mageproxy\Connector\Api\Data\OptimizationInterface $optimization */
    $optimization = $optimizationCollection->getNewEmptyItem();
    $optimization->setMinifyHtml(true);
    $optimization->setMinifyJs(true);
    $optimization->setUuid(strtolower($sourceModel->getLabel($i)));
    $optimization->setStatus($i);
    $optimization->setStoreId(Store::DISTRO_STORE_ID);
    $optimization->setRecordingId((int) $recording->getId());
    $optimizationCollection->addItem($optimization);
}
$optimizationCollection->save();
