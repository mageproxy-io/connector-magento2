<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model;

use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\Data\RecordingInterfaceFactory;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Model\ResourceModel\Recording;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Mageproxy\Connector\Model\Recording
 */
class RecordingTest extends TestCase
{
    private ?RecordingInterfaceFactory $recordingFactory = null;

    protected function setUp(): void
    {
        parent::setUp();
        $objectManager = Bootstrap::getObjectManager();
        $this->recordingFactory = $objectManager->get(RecordingInterfaceFactory::class);
    }

    /**
     * @covers \Mageproxy\Connector\Model\Recording::setStatus
     */
    public function testSetStatus(): void
    {
        $recording = $this->recordingFactory->create();
        $recording->setStatus(1);
        self::assertSame(1, $recording->getData('status'));
    }

    /**
     * @covers \Mageproxy\Connector\Model\Recording::getStatus
     */
    public function testGetStatus(): void
    {
        $recording = $this->recordingFactory->create();
        $recording->setData('status', '1');
        self::assertSame(1, $recording->getStatus());
    }

    /**
     * @covers \Mageproxy\Connector\Model\Recording::getStoreId
     */
    public function testGetStoreId(): void
    {
        $recording = $this->recordingFactory->create();
        $recording->setData('store_id', '1');
        self::assertSame(1, $recording->getStoreId());
    }

    /**
     * @covers \Mageproxy\Connector\Model\Recording::setStoreId
     */
    public function testSetStoreId(): void
    {
        $recording = $this->recordingFactory->create();
        $recording->setStoreId(2);
        self::assertSame(2, $recording->getData('store_id'));
    }

    /**
     * @covers \Mageproxy\Connector\Model\Recording::getUuid
     */
    public function testGetUuid(): void
    {
        $uuid = Bootstrap::getObjectManager()
            ->get(IdentityGeneratorInterface::class)
            ->generateId();
        $recording = $this->recordingFactory->create();
        $recording->setData('uuid', $uuid);
        self::assertSame($uuid, $recording->getUuid());
    }

    /**
     * @covers \Mageproxy\Connector\Model\Recording::setUuid
     */
    public function testSetUuid(): void
    {
        $uuid = Bootstrap::getObjectManager()
            ->get(IdentityGeneratorInterface::class)
            ->generateId();
        $recording = $this->recordingFactory->create();
        $recording->setUuid($uuid);
        self::assertSame($uuid, $recording->getData('uuid'));
    }

    /**
     * @covers \Mageproxy\Connector\Model\Recording::setScheduledAt
     */
    public function testSetScheduledAt(): void
    {
        $scheduledAt = '2024-01-01 00:00:00';
        $recording = $this->recordingFactory->create();
        $recording->setScheduledAt($scheduledAt);
        self::assertSame($scheduledAt, $recording->getData('scheduled_at'));
    }

    /**
     * @covers \Mageproxy\Connector\Model\Recording::getScheduledAt
     */
    public function testGetScheduledAt(): void
    {
        $scheduledAt = '2024-01-01 00:00:00';
        $recording = $this->recordingFactory->create();
        $recording->setData('scheduled_at', $scheduledAt);
        self::assertSame($scheduledAt, $recording->getScheduledAt());
    }

    /**
     * @covers \Mageproxy\Connector\Model\Recording::setDuration
     */
    public function testSetDuration(): void
    {
        $recording = $this->recordingFactory->create();
        $recording->setDuration(90);
        self::assertSame(90, $recording->getData('duration'));
    }

    /**
     * @covers \Mageproxy\Connector\Model\Recording::getDuration
     */
    public function testGetDuration(): void
    {
        $recording = $this->recordingFactory->create();
        $recording->setData('duration', '90');
        self::assertSame(90, $recording->getDuration());
    }

    /**
     * @covers \Mageproxy\Connector\Model\Recording::getIncludeTimestamp
     */
    public function testGetIncludeTimestamp(): void
    {
        $recording = $this->recordingFactory->create();
        $recording->setData('include_timestamp', '1');
        self::assertTrue($recording->getIncludeTimestamp());
    }

    /**
     * @covers \Mageproxy\Connector\Model\Recording::setIncludeTimestamp
     */
    public function testSetIncludeTimestamp(): void
    {
        $recording = $this->recordingFactory->create();
        $recording->setIncludeTimestamp(false);
        self::assertSame(false, $recording->getData('include_timestamp'));
    }

    public function testGetIdentities(): void
    {
        /** @var \Mageproxy\Connector\Model\Recording $recording */
        $recording = $this->recordingFactory->create();
        self::assertSame(['FPC'], $recording->getIdentities());
    }

    public function testUsesCorrectResourceModel(): void
    {
        $recording = $this->recordingFactory->create();
        self::assertSame(Recording::class, $recording->getResourceName());
    }

    public function testGetAndSetStaticVersion(): void
    {
        $recording = $this->recordingFactory->create();
        self::assertTrue(method_exists($recording, 'getStaticVersion'));
        self::assertTrue(method_exists($recording, 'setStaticVersion'));
        $version = (string) time();
        $recording->setStaticVersion($version);
        self::assertSame($version, $recording->getStaticVersion());
    }

    public function testGetAndSetStartedAt(): void
    {
        $recording = $this->recordingFactory->create();
        self::assertTrue(method_exists($recording, 'getStartedAt'));
        self::assertTrue(method_exists($recording, 'setStartedAt'));
        $startedAt = '2024-01-01 00:00:00';
        $recording->setStartedAt($startedAt);
        self::assertSame($startedAt, $recording->getStartedAt());
    }

    public function testGetAndSetFinishedAt(): void
    {
        $recording = $this->recordingFactory->create();
        self::assertTrue(method_exists($recording, 'getFinishedAt'));
        self::assertTrue(method_exists($recording, 'setFinishedAt'));
        $finishedAt = '2024-01-01 00:00:00';
        $recording->setFinishedAt($finishedAt);
        self::assertSame($finishedAt, $recording->getFinishedAt());
    }

    public function testGetAndSetErrorMessage(): void
    {
        $recording = $this->recordingFactory->create();
        self::assertTrue(method_exists($recording, 'getErrorMessage'));
        self::assertTrue(method_exists($recording, 'setErrorMessage'));
        $errorMessage = 'Error message';
        $recording->setErrorMessage($errorMessage);
        self::assertSame($errorMessage, $recording->getErrorMessage());
    }

    public function testGetAndSetStartCount(): void
    {
        $recording = $this->recordingFactory->create();
        self::assertTrue(method_exists($recording, 'getStartCount'));
        self::assertTrue(method_exists($recording, 'setStartCount'));
        $startCount = 1;
        $recording->setStartCount($startCount);
        self::assertSame($startCount, $recording->getStartCount());
    }

    public function testGetAndSetInitiator(): void
    {
        $recording = $this->recordingFactory->create();
        self::assertTrue(method_exists($recording, 'getInitiator'));
        self::assertTrue(method_exists($recording, 'setInitiator'));
        $initiator = 'admin';
        $recording->setInitiator($initiator);
        self::assertSame($initiator, $recording->getInitiator());
    }

    public function testGetAndSetDepsCnt(): void
    {
        $recording = $this->recordingFactory->create();
        self::assertTrue(method_exists($recording, 'getDepsCnt'));
        self::assertTrue(method_exists($recording, 'setDepsCnt'));
        $depsCnt = 1;
        $recording->setDepsCnt($depsCnt);
        self::assertSame($depsCnt, $recording->getDepsCnt());
    }

    public function testGetAndSetRecordSchedule(): void
    {
        $recording = $this->recordingFactory->create();
        self::assertTrue(method_exists($recording, 'getRecordSchedule'));
        self::assertTrue(method_exists($recording, 'setRecordSchedule'));
        $schedule = [
            [
                'record_for' => 20,
                'record_time_unit' => 'm',
                'pause_for' => 5,
                'pause_time_unit' > 'm'
            ],
            [
                'record_for' => 20,
                'record_time_unit' => 'm',
                'pause_for' => 5,
                'pause_time_unit' > 'm'
            ]
        ];
        $recording->setRecordSchedule($schedule);
        self::assertSame($schedule, $recording->getRecordSchedule());
    }

    public function testGetAndSetPageHandlePriority(): void
    {
        $recording = $this->recordingFactory->create();
        self::assertTrue(method_exists($recording, 'getPageHandlePriority'));
        self::assertTrue(method_exists($recording, 'setPageHandlePriority'));
        $priority = ['home', 'category'];
        $recording->setPageHandlePriority($priority);
        self::assertSame($priority, $recording->getPageHandlePriority());
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_running.php
     */
    public function testGetLifetime(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $recording = $objectManager->get(RecordingRepositoryInterface::class)->get('running');
        $startedAt = (new \DateTime())->modify('-10 minutes')->format('Y-m-d H:i:s');
        $recording->setStartedAt($startedAt);
        $recording->setDuration(20);
        self::assertSame(600, $recording->getLifetime());
    }
}
