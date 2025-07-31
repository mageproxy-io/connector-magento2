<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

/*
 * Simulates a new recording
 */

use Magento\Framework\App\View\Deployment\Version;
use Magento\Store\Model\StoreManagerInterface;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;

/** @var \Magento\TestFramework\ObjectManager $objectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$deployedVersion = $objectManager->get(Version::class);

$recording = $objectManager->create(RecordingInterface::class);
$storeId = (int) $objectManager->get(StoreManagerInterface::class)->getStore()->getId();
$recording->setStoreId($storeId);
$recording->setUuid('pending');
$recording->setDuration(60);
$recording->setIncludeTimestamp(true);
$recording->setStatus(RecordingInterface::STATUS_PENDING);
$recording->setStaticVersion($deployedVersion->getValue());
$recording->setScheduledAt(
    (new DateTime())
        ->modify('+5 minutes')
        ->format('Y-m-d H:i:s')
);
$objectManager->get(RecordingRepositoryInterface::class)->save($recording);
