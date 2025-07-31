<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\Store\Model\Store;
use Mageproxy\Connector\Api\Data\RecordingInterfaceFactory;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$recordingFactory = $objectManager->create(RecordingInterfaceFactory::class);
$repository = $objectManager->create(RecordingRepositoryInterface::class);

for ($i = 0; $i < 6; $i++) {
    /** @var \Mageproxy\Connector\Model\Recording $recording */
    $recording = $recordingFactory->create();
    $recording->setStatus($i);
    $recording->setUuid($objectManager->get(IdentityGeneratorInterface::class)->generateId());
    $recording->setStoreId(Store::DISTRO_STORE_ID);
    $recording->setScheduledAt((new DateTime())->modify("+{$i} hours")->format("Y-m-d H:i:s"));
    $recording->setIncludeTimestamp($i % 2 === 0);
    $recording->setDuration(60 - $i * 5);
    $repository->save($recording);
}
