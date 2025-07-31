<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\ResourceModel;

use DateTime;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Model\ResourceModel\Recording;
use PHPUnit\Framework\TestCase;

class RecordingTest extends TestCase
{
    public function testItDoesNotAllowRecordingToBeSavedIfAnotherPendingRecordingForTheSameStoreWithOverlappingInterval()
    {
        $this->expectException(LocalizedException::class);

        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $resourceModel = $objectManager->get(Recording::class);

        $recording1 = $objectManager->create(RecordingInterface::class);
        $recording1->setStoreId(1);
        $recording1->setDuration(60);
        $recording1->setUuid('uuid-1');
        $recording1->setStatus(0);
        $recording1->setScheduledAt((new DateTime())->modify('+5 minutes')->format('Y-m-d H:i:s'));
        $resourceModel->save($recording1);

        $recording2 = $objectManager->create(RecordingInterface::class);
        $recording2->setScheduledAt((new DateTime())->modify('+10 minutes')->format('Y-m-d H:i:s'));
        $recording2->setStoreId(1);
        $recording2->setStatus(0);
        $resourceModel->save($recording2);
    }

    public function testItDoesNotAllowRecordingToBeSavedIfAnotherRunningRecordingForTheSameStoreWithOverlappingInterval(
    ): void
    {
        $this->expectException(LocalizedException::class);

        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $resourceModel = $objectManager->get(Recording::class);

        $recording1 = $objectManager->create(RecordingInterface::class);
        $recording1->setStoreId(1);
        $recording1->setDuration(60);
        $recording1->setUuid('uuid-1');
        $recording1->setStatus(1); // running
        $recording1->setScheduledAt((new DateTime())->modify('-10 minutes')->format('Y-m-d H:i:s'));
        $recording1->setStartedAt((new DateTime())->modify('-8 minutes')->format('Y-m-d H:i:s'));

        $resourceModel->save($recording1);

        $recording2 = $objectManager->create(RecordingInterface::class);
        $recording2->setScheduledAt((new DateTime())->modify('+10 minutes')->format('Y-m-d H:i:s'));
        $recording2->setDuration(60);
        $recording2->setStoreId(1);
        $recording2->setStatus(0);
        $resourceModel->save($recording2);
    }

    public function testItSetsProperties(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $recording = $objectManager->create(RecordingInterface::class);
        $recording->setDuration(60);
        $resourceModel = $objectManager->get(\Mageproxy\Connector\Model\ResourceModel\Recording::class);
        $resourceModel->save($recording);
        self::assertNotEmpty($recording->getUuid());
        self::assertSame(RecordingInterface::STATUS_PENDING, $recording->getStatus());
        self::assertNotNull($recording->getScheduledAt());
        self::assertNotNull($recording->getStoreId());
    }

    public function testPageHandlePrioritySerialization(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $recording = $objectManager->create(RecordingInterface::class);
        $pageHandlePriority = ['home', 'product', 'category'];
        $recording->setPageHandlePriority($pageHandlePriority);
        $resourceModel = $objectManager->get(\Mageproxy\Connector\Model\ResourceModel\Recording::class);
        $resourceModel->save($recording);
        $id = $recording->getId();
        $recording = $objectManager->create(RecordingInterface::class);
        $resourceModel->load($recording, $id);
        self::assertSame($pageHandlePriority, $recording->getPageHandlePriority());
    }
}
