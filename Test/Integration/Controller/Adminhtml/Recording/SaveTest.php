<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Controller\Adminhtml\Recording;

use Magento\Framework\App\Request\Http;
use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Mageproxy\Connector\Model\ApiClient\PostNewRecordingInterface;
use Mageproxy\Connector\Model\ApiClient\PostNewRecordingResponseInterface;
use Mageproxy\Connector\Model\ResourceModel\Recording\Collection;

/**
 * @covers \Mageproxy\Connector\Controller\Adminhtml\Recording\Save
 * @magentoAppArea adminhtml
 */
class SaveTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    protected function setUp(): void
    {
        $this->resource = 'Mageproxy_Connector::recording_create';
        $this->uri = 'backend/mageproxy/recording/save';
        $this->httpMethod = Http::METHOD_POST;
        parent::setUp();
    }

    public function testAclHasAccess()
    {
        $this->markTestSkipped('Subsequently tested with the tests below.');
    }

    /**
     * @magentoAppArea adminhtml
     * @magentoConfigFixture default/mageproxy_connector/settings/service_id 1
     */
    public function testSuccessfulDispatch(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $postNewRecordingMock = $this->createMock(PostNewRecordingInterface::class);
        $response = $objectManager->create(PostNewRecordingResponseInterface::class);

        $uuid = $objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $response->setId($uuid);

        $postNewRecordingMock
            ->expects($this->once())
            ->method('execute')
            ->with(1)
            ->willReturn($response);

        $objectManager->addSharedInstance($postNewRecordingMock, PostNewRecordingInterface::class, true);

        // Body for scheduled save
        // Mimic Date.prototype.toISOString() in JavaScript

        $scheduledAtDt = (new \DateTime())->modify('+5 minutes');
        $this->getRequest()->setMethod(Http::METHOD_POST)
            ->setPostValue([
                'store_id' => [Store::DISTRO_STORE_ID],
                'scheduled_at' => $scheduledAtDt->format('Y-m-d\TH:i:s.v') . 'Z'
            ]);

        $this->dispatch($this->uri);

        $recordingCollection = $objectManager->create(Collection::class);
        /** @var \Mageproxy\Connector\Model\Recording $recording */
        $recording = $recordingCollection->getFirstItem();
        self::assertSame($uuid, $recording->getUuid());
        self::assertSame(Store::DISTRO_STORE_ID, $recording->getStoreId());
        self::assertSame($scheduledAtDt->format('Y-m-d H:i:s'), $recording->getScheduledAt());
        self::assertSame(5, $recording->getDuration());
        self::assertFalse($recording->getIncludeTimestamp());

        $successMessages = $this->getSessionMessages(MessageInterface::TYPE_SUCCESS);
        self::assertNotEmpty($successMessages);
        $this->assertRedirect($this->stringContains('backend/mageproxy/recording/index'));
    }

    /**
     * @magentoAppArea adminhtml
     * @magentoConfigFixture default/mageproxy_connector/settings/service_id 1
     * @magentoDataFixture   Magento/Store/_files/second_store.php
     */
    public function testSuccessfulMultiStoreDispatch(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $postNewRecordingMock = $this->createMock(PostNewRecordingInterface::class);
        $storeManager = $objectManager->get(StoreManagerInterface::class);
        $defaultStoreId = Store::DISTRO_STORE_ID;
        $secondStoreId = (int) $storeManager->getStore('fixture_second_store')->getId();

        $responseFirst = $objectManager->create(PostNewRecordingResponseInterface::class);
        $uuidFirst = $objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $responseFirst->setId($uuidFirst);

        $responseSecond = $objectManager->create(PostNewRecordingResponseInterface::class);
        $uuidSecond = $objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $responseSecond->setId($uuidSecond);

        $postNewRecordingMock
            ->expects($this->exactly(2))
            ->method('execute')
            ->with(1)
            ->willReturn($responseFirst, $responseSecond);

        $objectManager->addSharedInstance($postNewRecordingMock, PostNewRecordingInterface::class, true);

        // Body for scheduled save
        // Mimic Date.prototype.toISOString() in JavaScript

        $scheduledAtDt = (new \DateTime())->modify('+5 minutes');
        $this->getRequest()->setMethod(Http::METHOD_POST)
            ->setPostValue([
                'store_id' => [$defaultStoreId, $secondStoreId],
                'scheduled_at' => $scheduledAtDt->format('Y-m-d\TH:i:s.v') . 'Z'
            ]);

        $this->dispatch($this->uri);

        $recordingCollection = $objectManager->create(Collection::class);
        /** @var \Mageproxy\Connector\Model\Recording $recording */
        $recording = $recordingCollection->getItemsByColumnValue('store_id', $defaultStoreId)[0];
        self::assertSame($uuidFirst, $recording->getUuid());
        self::assertSame($defaultStoreId, $recording->getStoreId());
        self::assertSame($scheduledAtDt->format('Y-m-d H:i:s'), $recording->getScheduledAt());
        self::assertSame(5, $recording->getDuration());
        self::assertFalse($recording->getIncludeTimestamp());

        /** @var \Mageproxy\Connector\Model\Recording $recording */
        $recording = $recordingCollection->getItemsByColumnValue('store_id', $secondStoreId)[0];
        self::assertSame($uuidSecond, $recording->getUuid());
        self::assertSame($secondStoreId, $recording->getStoreId());
        self::assertSame($scheduledAtDt->format('Y-m-d H:i:s'), $recording->getScheduledAt());
        self::assertSame(5, $recording->getDuration());
        self::assertFalse($recording->getIncludeTimestamp());

        $successMessages = $this->getSessionMessages(MessageInterface::TYPE_SUCCESS);
        self::assertNotEmpty($successMessages);
        $this->assertRedirect($this->stringContains('backend/mageproxy/recording/index'));
    }

    /**
     * @magentoAppArea adminhtml
     * @magentoConfigFixture default/mageproxy_connector/settings/service_id 1
     * @magentoDataFixture   Magento/Store/_files/second_store.php
     */
    public function testSuccessfulAllStoreDispatch(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $postNewRecordingMock = $this->createMock(PostNewRecordingInterface::class);
        $storeManager = $objectManager->get(StoreManagerInterface::class);
        $defaultStoreId = Store::DISTRO_STORE_ID;
        $secondStoreId = (int) $storeManager->getStore('fixture_second_store')->getId();

        $responseFirst = $objectManager->create(PostNewRecordingResponseInterface::class);
        $uuidFirst = $objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $responseFirst->setId($uuidFirst);

        $responseSecond = $objectManager->create(PostNewRecordingResponseInterface::class);
        $uuidSecond = $objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $responseSecond->setId($uuidSecond);

        $postNewRecordingMock
            ->expects($this->exactly(2))
            ->method('execute')
            ->with(1)
            ->willReturn($responseFirst, $responseSecond);

        $objectManager->addSharedInstance($postNewRecordingMock, PostNewRecordingInterface::class, true);

        // Body for scheduled save
        // Mimic Date.prototype.toISOString() in JavaScript

        $scheduledAtDt = (new \DateTime())->modify('+5 minutes');
        $this->getRequest()->setMethod(Http::METHOD_POST)
            ->setPostValue([
                'store_id' => ['0'],
                'scheduled_at' => $scheduledAtDt->format('Y-m-d\TH:i:s.v') . 'Z'
            ]);

        $this->dispatch($this->uri);

        $recordingCollection = $objectManager->create(Collection::class);
        /** @var \Mageproxy\Connector\Model\Recording $recording */
        $recording = $recordingCollection->getItemsByColumnValue('store_id', $defaultStoreId)[0];
        self::assertSame($uuidFirst, $recording->getUuid());
        self::assertSame($defaultStoreId, $recording->getStoreId());
        self::assertSame($scheduledAtDt->format('Y-m-d H:i:s'), $recording->getScheduledAt());
        self::assertSame(5, $recording->getDuration());
        self::assertFalse($recording->getIncludeTimestamp());

        /** @var \Mageproxy\Connector\Model\Recording $recording */
        $recording = $recordingCollection->getItemsByColumnValue('store_id', $secondStoreId)[0];
        self::assertSame($uuidSecond, $recording->getUuid());
        self::assertSame($secondStoreId, $recording->getStoreId());
        self::assertSame($scheduledAtDt->format('Y-m-d H:i:s'), $recording->getScheduledAt());
        self::assertSame(5, $recording->getDuration());
        self::assertFalse($recording->getIncludeTimestamp());

        $successMessages = $this->getSessionMessages(MessageInterface::TYPE_SUCCESS);
        self::assertNotEmpty($successMessages);
        $this->assertRedirect($this->stringContains('backend/mageproxy/recording/index'));
    }

    /**
     * @magentoAppArea adminhtml
     * @magentoConfigFixture default/mageproxy_connector/settings/service_id 1
     */
    public function testFailOnPastScheduledDispatch(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $postNewRecordingMock = $this->createMock(PostNewRecordingInterface::class);

        $postNewRecordingMock
            ->expects($this->never())
            ->method('execute');

        $objectManager->addSharedInstance($postNewRecordingMock, PostNewRecordingInterface::class, true);

        // Body for scheduled save
        // Mimic Date.prototype.toISOString() in JavaScript

        $scheduledAtDt = (new \DateTime())->modify('-5 minutes');
        $this->getRequest()->setMethod(Http::METHOD_POST)
            ->setPostValue([
                'store_id' => [Store::DISTRO_STORE_ID],
                'scheduled_at' => $scheduledAtDt->format('Y-m-d\TH:i:s.v') . 'Z'
            ]);

        $this->dispatch($this->uri);

        $errorMessages = $this->getSessionMessages(MessageInterface::TYPE_ERROR);
        self::assertNotEmpty($errorMessages);
        $this->assertRedirect($this->stringContains('backend/mageproxy/recording/create'));
    }

}
