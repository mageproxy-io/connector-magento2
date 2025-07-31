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

use Magento\Framework\App\View\Deployment\Version;
use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\RecordingManagerInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Model\ApiClient\PostNewRecordingInterface;
use Mageproxy\Connector\Model\ApiClient\PostNewRecordingResponseInterface;
use Mageproxy\Connector\Model\ResourceModel\Recording\Collection;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Mageproxy\Connector\Model\RecordingManager
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 * @magentoAppArea adminhtml
 */
class RecordingManagerTest extends TestCase
{
    protected $objectManager;
    protected $dateTimeFmt;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->dateTimeFmt = $this->objectManager->get(\Magento\Framework\Stdlib\DateTime::class);
    }

    public function testShouldStartRecording(): void
    {
        $sut = $this->objectManager->create(RecordingManagerInterface::class);
        $recording = $this->objectManager->create(RecordingInterface::class);

        // Scheduled datetime reached and within duration
        $recording->setData([
            'status' => RecordingInterface::STATUS_PENDING,
            'scheduled_at' => $this->dateTimeFmt->formatDate(time() - 1 * 60),
            'duration' => 30
        ]);
        $this->assertTrue($sut->shouldStart($recording));

        // Scheduled datetime not reached yet
        $recording->setData([
            'status' => RecordingInterface::STATUS_PENDING,
            'scheduled_at' => $this->dateTimeFmt->formatDate(time() + 2 * 60),
            'duration' => 30
        ]);
        $this->assertFalse($sut->shouldStart($recording));

        // Schedule datetime reached and within duration, but already running...
        $recording->setData([
            'status' => RecordingInterface::STATUS_RUNNING,
            'started_at' => $this->dateTimeFmt->formatDate(time() - 2 * 60),
            'scheduled_at' => $this->dateTimeFmt->formatDate(time() - 2 * 60),
            'duration' => 30
        ]);
        $this->assertFalse($sut->shouldStart($recording));

        // Scheduled datetime + duration exceeded
        $recording->setData([
            'status' => RecordingInterface::STATUS_PENDING,
            'scheduled_at' => $this->dateTimeFmt->formatDate(time() - (31 * 60)),
            'duration' => 30
        ]);
        $this->assertFalse($sut->shouldStart($recording));
    }

    public function testShouldStopRecording(): void
    {
        $sut = $this->objectManager->create(RecordingManagerInterface::class);
        $recording = $this->objectManager->create(RecordingInterface::class);

        $recording->setData([
            'status' => RecordingInterface::STATUS_RUNNING,
            'scheduled_at' => (new \DateTime())->modify('-31 minutes')->format('Y-m-d H:i:s'),
            'started_at' => (new \DateTime())->modify('-31 minutes')->format('Y-m-d H:i:s'),
            'duration' => 30
        ]);
        $this->assertTrue($sut->shouldStop($recording));

        $recording->setData([
            'status' => RecordingInterface::STATUS_RUNNING,
            'scheduled_at' => (new \DateTime())->modify('-20 minutes')->format('Y-m-d H:i:s'),
            'started_at' => (new \DateTime())->modify('-19 minutes')->format('Y-m-d H:i:s'),
            'duration' => 30
        ]);
        $this->assertFalse($sut->shouldStop($recording));
    }

    public function testRecordingWasMissed(): void
    {
        $sut = $this->objectManager->create(RecordingManagerInterface::class);
        $missedRecording = $this->objectManager->create(RecordingInterface::class);
        $missedRecording->setData([
            'status' => RecordingInterface::STATUS_PENDING,
            'scheduled_at' => $this->dateTimeFmt->formatDate(time() - 61*60), // 61 minutes ago
            'duration' => 60
        ]);
        $this->assertTrue($sut->wasMissed($missedRecording));

        $futureRecording = $this->objectManager->create(RecordingInterface::class);
        $futureRecording->setData([
            'status' => RecordingInterface::STATUS_PENDING,
            'scheduled_at' => $this->dateTimeFmt->formatDate(time() + 60),
            'duration' => 60
        ]);
        $this->assertFalse($sut->wasMissed($futureRecording));
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_pending.php
     */
    public function testStartRecordingChangesStatus(): void
    {
        $sut = $this->objectManager->create(RecordingManagerInterface::class);
        $recording = $this->objectManager->get(RecordingRepositoryInterface::class)->get('pending');
        $recording->setScheduledAt($this->dateTimeFmt->formatDate(time() - 60)); // should have started 60 seconds ago
        $sut->start($recording);

        $this->assertSame(RecordingInterface::STATUS_RUNNING, $recording->getStatus());
    }

    public function testStartRecordingThrowsExceptionIfAlreadyStarted(): void
    {
        $this->expectException(LocalizedException::class);
        $sut = $this->objectManager->create(RecordingManagerInterface::class);
        $recording = $this->objectManager->create(RecordingInterface::class);
        $recording->setStatus(RecordingInterface::STATUS_RUNNING);
        $sut->start($recording);
    }

    public function testStartRecordingWhenOtherRecordingRunningForTheSameStoreThrowsException(): void
    {
        $this->expectException(LocalizedException::class);
        $sut = $this->objectManager->create(RecordingManagerInterface::class);
        $recording = $this->objectManager->create(RecordingInterface::class);
        $recording->setStatus(RecordingInterface::STATUS_PENDING);
        $recording->setStoreId(Store::DISTRO_STORE_ID);
        $recording->setScheduledAt($this->dateTimeFmt->formatDate(time() - 60)); // should have started 60 seconds ago
        $sut->start($recording);

        $recording2 = $this->objectManager->create(RecordingInterface::class);
        $recording2->setStatus(RecordingInterface::STATUS_PENDING);
        $recording2->setStoreId(Store::DISTRO_STORE_ID);
        $recording->setScheduledAt($this->dateTimeFmt->formatDate(time() - 30)); // should have started 60 seconds ago
        $sut->start($recording2);
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_pending.php
     * @magentoConfigFixture default/mageproxy_connector/settings/run_mode manual
     */
    public function testInProgressForDefaultStore(): void
    {
        $sut = $this->objectManager->create(RecordingManagerInterface::class);
        $this->assertFalse($sut->isInProgress());
        $recording = $this->objectManager->get(RecordingRepositoryInterface::class)->get('pending');
        $recording->setScheduledAt($this->dateTimeFmt->formatDate(time() - 60)); // should have started 60 seconds ago
        $sut->start($recording);
        $this->assertTrue($sut->isInProgress());
        $sut->stop($recording);
        $this->assertFalse($sut->isInProgress());
    }

    /**
     * @magentoDataFixture Magento/Store/_files/second_store.php
     * @magentoConfigFixture default/mageproxy_connector/settings/run_mode manual
     */
    public function testInProgressForSpecificStore(): void
    {
        $sut = $this->objectManager->create(RecordingManagerInterface::class);
        $storeManager = $this->objectManager->get(StoreManagerInterface::class);
        $secondStoreId = (int) $storeManager->getStore('fixture_second_store')->getId();

        $deployedVersion = $this->objectManager->get(Version::class);

        $recording = $this->objectManager->create(RecordingInterface::class);
        $recording->setDuration(60);
        $recording->setScheduledAt($this->dateTimeFmt->formatDate(time() - 60));
        $recording->setStoreId($secondStoreId);
        $recording->setStaticVersion($deployedVersion->getValue());
        $sut->start($recording);
        $this->assertTrue($sut->isInProgress($secondStoreId));
        $sut->stop($recording);
        $this->assertFalse($sut->isInProgress($secondStoreId));
    }

    /**
     * @magentoDataFixture Magento/Store/_files/second_store.php
     */
    public function testStartRecordingsForDifferentStores(): void
    {
        $sut = $this->objectManager->create(RecordingManagerInterface::class);
        $storeManager = $this->objectManager->get(StoreManagerInterface::class);
        $defaultStoreId = Store::DISTRO_STORE_ID;
        $secondStoreId = (int) $storeManager->getStore('fixture_second_store')->getId();

        $deployedVersion = $this->objectManager->get(Version::class);

        // Start a recording for the default store
        $recording = $this->objectManager->create(RecordingInterface::class);
        $recording->setDuration(60);
        $recording->setScheduledAt($this->dateTimeFmt->formatDate(time() - 60));
        $recording->setStoreId($defaultStoreId);
        $recording->setStaticVersion($deployedVersion->getValue());
        $sut->start($recording);
        $this->assertTrue($sut->isInProgress($defaultStoreId));
        $this->assertFalse($sut->isInProgress($secondStoreId));

        // Start a recording for the second store
        $recording2 = $this->objectManager->create(RecordingInterface::class);
        $recording2->setDuration(60);
        $recording2->setScheduledAt($this->dateTimeFmt->formatDate(time() - 60));
        $recording2->setStoreId($secondStoreId);
        $recording2->setStaticVersion($deployedVersion->getValue());
        $sut->start($recording2);
        $this->assertTrue($sut->isInProgress($secondStoreId));
        $this->assertTrue($sut->isInProgress($defaultStoreId));
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/run_mode manual
     */
    public function testStopRecording(): void
    {
        $sut = $this->objectManager->create(RecordingManagerInterface::class);
        $deployedVersion = $this->objectManager->get(Version::class);
        $recording = $this->objectManager->create(RecordingInterface::class);
        $recording->setStatus(RecordingInterface::STATUS_PENDING);
        $recording->setScheduledAt($this->dateTimeFmt->formatDate(time() - 2*60)); // started 2 min ago
        $recording->setDuration(20);
        $recording->setStoreId(Store::DISTRO_STORE_ID);
        $recording->setStaticVersion($deployedVersion->getValue());
        $sut->start($recording);
        $sut->stop($recording);
        $this->assertSame(RecordingInterface::STATUS_STOPPED, $recording->getStatus());
        $this->assertNotNull($recording->getFinishedAt());
        $this->assertSame($this->dateTimeFmt->formatDate(time()), $recording->getFinishedAt());

        $this->expectException(LocalizedException::class);
        $sut->stop($recording);
    }

    public function testGetRunningRecording(): void
    {
        $sut = $this->objectManager->create(RecordingManagerInterface::class);
        $version = $this->objectManager->get(Version::class);
        $this->assertNull($sut->getRunning());
        $recording = $this->objectManager->create(RecordingInterface::class);
        $recording->setStatus(RecordingInterface::STATUS_PENDING);
        $recording->setScheduledAt($this->dateTimeFmt->formatDate(time() - 2*60)); // should have started 2 min ago
        $recording->setDuration(20);
        $recording->setStoreId(Store::DISTRO_STORE_ID);
        $recording->setStaticVersion($version->getValue());
        $sut->start($recording);
        $this->assertSame($recording->getUuid(), $sut->getRunning()->getUuid());
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/run_mode manual
     * @magentoConfigFixture default/mageproxy_connector/settings/service_id 123
     */
    public function testScheduleOneNewRecordings(): void
    {
        $postNewRecordingMock = $this->createMock(PostNewRecordingInterface::class);

        $response = $this->objectManager->create(PostNewRecordingResponseInterface::class);
        $uuid = $this->objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $response->setId($uuid);

        $postNewRecordingMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn($response);

        $this->objectManager->addSharedInstance($postNewRecordingMock, PostNewRecordingInterface::class, true);

        $sut = $this->objectManager->create(RecordingManagerInterface::class);
        $scheduledAtDt = (new \DateTime())->modify('+5 minutes');
        $sut->createNewRecordings([Store::DISTRO_STORE_ID], 5, strtotime($scheduledAtDt->format('Y-m-d\TH:i:s.v') . 'Z'));

        $recordingCollection = $this->objectManager->create(Collection::class);
        /** @var \Mageproxy\Connector\Model\Recording $recording */
        $recording = $recordingCollection->getFirstItem();
        self::assertSame($uuid, $recording->getUuid());
        self::assertSame(Store::DISTRO_STORE_ID, $recording->getStoreId());
        self::assertSame($scheduledAtDt->format('Y-m-d H:i:s'), $recording->getScheduledAt());
        self::assertSame(5, $recording->getDuration());
        self::assertFalse($recording->getIncludeTimestamp());
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/service_id 1
     * @magentoConfigFixture default/mageproxy_connector/settings/run_mode manual
     * @magentoDataFixture   Magento/Store/_files/second_store.php
     */
    public function testScheduleMultiNewRecordings(): void
    {
        $postNewRecordingMock = $this->createMock(PostNewRecordingInterface::class);

        $storeManager = $this->objectManager->get(StoreManagerInterface::class);
        $defaultStoreId = Store::DISTRO_STORE_ID;
        $secondStoreId = (int) $storeManager->getStore('fixture_second_store')->getId();

        $responseFirst = $this->objectManager->create(PostNewRecordingResponseInterface::class);
        $uuidFirst = $this->objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $responseFirst->setId($uuidFirst);

        $responseSecond = $this->objectManager->create(PostNewRecordingResponseInterface::class);
        $uuidSecond = $this->objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $responseSecond->setId($uuidSecond);

        $responseThird = $this->objectManager->create(PostNewRecordingResponseInterface::class);
        $uuidThird = $this->objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $responseThird->setId($uuidThird);

        $postNewRecordingMock
            ->expects($this->exactly(3))
            ->method('execute')
            ->willReturn($responseFirst, $responseSecond, $responseThird);

        $this->objectManager->addSharedInstance($postNewRecordingMock, PostNewRecordingInterface::class, true);

        $sut = $this->objectManager->create(RecordingManagerInterface::class);
        $scheduledAtDt = (new \DateTime())->modify('+5 minutes');
        $duration = 5;
        $sut->createNewRecordings(
            [$defaultStoreId, $secondStoreId],
            $duration,
            strtotime($scheduledAtDt->format('Y-m-d\TH:i:s.v') . 'Z')
        );

        $recordingCollection = $this->objectManager->create(Collection::class);
        /** @var \Mageproxy\Connector\Model\Recording $recording */
        $recording = $recordingCollection->getItemsByColumnValue('store_id', $defaultStoreId)[0];
        self::assertSame($uuidFirst, $recording->getUuid());
        self::assertSame($defaultStoreId, $recording->getStoreId());
        self::assertSame($scheduledAtDt->format('Y-m-d H:i:s'), $recording->getScheduledAt());
        self::assertSame($duration, $recording->getDuration());
        self::assertFalse($recording->getIncludeTimestamp());

        /** @var \Mageproxy\Connector\Model\Recording $recording */
        $recording = $recordingCollection->getItemsByColumnValue('store_id', $secondStoreId)[0];
        self::assertSame($uuidSecond, $recording->getUuid());
        self::assertSame($secondStoreId, $recording->getStoreId());
        self::assertSame($scheduledAtDt->format('Y-m-d H:i:s'), $recording->getScheduledAt());
        self::assertSame(5, $recording->getDuration());
        self::assertFalse($recording->getIncludeTimestamp());

        /** Trying to create another recording for same store may fail, just producing an error message */
        $sut->createNewRecordings([$secondStoreId], strtotime($scheduledAtDt->format('Y-m-d\TH:i:s.v') . 'Z'));

        $recordingCollection = $this->objectManager->create(Collection::class);
        /** @var \Mageproxy\Connector\Model\Recording $recording */
        $recording = $recordingCollection->getItemsByColumnValue('store_id', $secondStoreId)[0];
        self::assertNotEquals($uuidThird, $recording->getUuid());
        self::assertSame($secondStoreId, $recording->getStoreId());
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 1
     * @magentoConfigFixture default/mageproxy_connector/settings/service_id 1
     * @magentoConfigFixture default/mageproxy_connector/settings/run_mode manual
     * @magentoDataFixture   Magento/Store/_files/second_store.php
     */
    public function testScheduleAllStoresNewRecordings(): void
    {
        $postNewRecordingMock = $this->createMock(PostNewRecordingInterface::class);

        $storeManager = $this->objectManager->get(StoreManagerInterface::class);
        $defaultStoreId = Store::DISTRO_STORE_ID;
        $secondStoreId = (int) $storeManager->getStore('fixture_second_store')->getId();

        $responseFirst = $this->objectManager->create(PostNewRecordingResponseInterface::class);
        $uuidFirst = $this->objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $responseFirst->setId($uuidFirst);

        $responseSecond = $this->objectManager->create(PostNewRecordingResponseInterface::class);
        $uuidSecond = $this->objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $responseSecond->setId($uuidSecond);

        $responseThird = $this->objectManager->create(PostNewRecordingResponseInterface::class);
        $uuidThird = $this->objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $responseThird->setId($uuidThird);

        $postNewRecordingMock
            ->expects($this->exactly(3))
            ->method('execute')
            ->willReturn($responseFirst, $responseSecond, $responseThird);

        $this->objectManager->addSharedInstance($postNewRecordingMock, PostNewRecordingInterface::class, true);

        $sut = $this->objectManager->create(RecordingManagerInterface::class);
        $scheduledAtDt = (new \DateTime())->modify('+5 minutes');
        $duration = 5;
        $sut->createNewRecordings([$defaultStoreId, $secondStoreId], $duration, strtotime($scheduledAtDt->format('Y-m-d\TH:i:s.v') . 'Z'));

        $recordingCollection = $this->objectManager->create(Collection::class);
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

        /** Trying to create another recording for same store may fail, just producing an error message */
        $sut->createNewRecordings([$secondStoreId], strtotime($scheduledAtDt->format('Y-m-d\TH:i:s.v') . 'Z'));

        $recordingCollection = $this->objectManager->create(Collection::class);
        /** @var \Mageproxy\Connector\Model\Recording $recording */
        $recording = $recordingCollection->getItemsByColumnValue('store_id', $secondStoreId)[0];
        self::assertNotEquals($uuidThird, $recording->getUuid());
        self::assertSame($secondStoreId, $recording->getStoreId());
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/service_id 1
     * @magentoConfigFixture default/mageproxy_connector/settings/run_mode manual
     * @magentoDataFixture   Magento/Store/_files/second_store.php
     */
    public function testScheduleAllStoresNewRecordingsWithRollback(): void
    {
        $postNewRecordingMock = $this->createMock(PostNewRecordingInterface::class);

        $storeManager = $this->objectManager->get(StoreManagerInterface::class);
        $defaultStoreId = Store::DISTRO_STORE_ID;
        $secondStoreId = (int) $storeManager->getStore('fixture_second_store')->getId();

        $responseFirst = $this->objectManager->create(PostNewRecordingResponseInterface::class);
        $uuidFirst = $this->objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $responseFirst->setId($uuidFirst);

        $responseSecond = $this->objectManager->create(PostNewRecordingResponseInterface::class);
        $uuidSecond = $this->objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $responseSecond->setId($uuidSecond);

        $responseThird = $this->objectManager->create(PostNewRecordingResponseInterface::class);
        $uuidThird = $this->objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $responseThird->setId($uuidThird);

        $postNewRecordingMock
            ->expects($this->exactly(2))
            ->method('execute')
            ->willReturn($responseFirst, $responseSecond, $responseThird);

        $this->objectManager->addSharedInstance($postNewRecordingMock, PostNewRecordingInterface::class, true);

        $sut = $this->objectManager->create(RecordingManagerInterface::class);
        $scheduledAtDt = (new \DateTime())->modify('+5 minutes');
        $duration = 10;
        $sut->createNewRecordings([$defaultStoreId], $duration, strtotime($scheduledAtDt->format('Y-m-d\TH:i:s.v') . 'Z'), true);

        $recordingCollection = $this->objectManager->create(Collection::class);
        /** @var \Mageproxy\Connector\Model\Recording $recording */
        $recording = $recordingCollection->getItemsByColumnValue('store_id', $defaultStoreId)[0];
        self::assertSame($uuidFirst, $recording->getUuid());
        self::assertSame($defaultStoreId, $recording->getStoreId());
        self::assertSame($scheduledAtDt->format('Y-m-d H:i:s'), $recording->getScheduledAt());
        self::assertSame($duration, $recording->getDuration());
        self::assertFalse($recording->getIncludeTimestamp());

        $this->expectException(LocalizedException::class);
        $sut->createNewRecordings([$defaultStoreId, $secondStoreId], 5, strtotime($scheduledAtDt->format('Y-m-d\TH:i:s.v') . 'Z'), true);

        /** @var \Mageproxy\Connector\Model\Recording $recording */

        $recordingCollection = $this->objectManager->create(Collection::class);

        self::assertCount(0, $recordingCollection->getItemsByColumnValue('store_id', $secondStoreId));
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/service_id 1
     * @magentoConfigFixture default/mageproxy_connector/settings/run_mode manual
     */
    public function testStartOneNewRecordings(): void
    {
        $postNewRecordingMock = $this->createMock(PostNewRecordingInterface::class);
        $response = $this->objectManager->create(PostNewRecordingResponseInterface::class);

        $uuid = $this->objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $response->setId($uuid);

        $postNewRecordingMock
            ->expects($this->once())
            ->method('execute')
            ->with(1)
            ->willReturn($response);

        $this->objectManager->addSharedInstance($postNewRecordingMock, PostNewRecordingInterface::class, true);

        $sut = $this->objectManager->create(RecordingManagerInterface::class);
        $scheduledAtDt = (new \DateTime())->format('Y-m-d H:i:s');
        $sut->createNewRecordings([Store::DISTRO_STORE_ID]);

        $recordingCollection = $this->objectManager->create(Collection::class);
        /** @var \Mageproxy\Connector\Model\Recording $recording */
        $recording = $recordingCollection->getFirstItem();
        self::assertSame($uuid, $recording->getUuid());
        self::assertSame(Store::DISTRO_STORE_ID, $recording->getStoreId());
        self::assertSame($scheduledAtDt, $recording->getScheduledAt());
        self::assertSame(5, $recording->getDuration());
        self::assertFalse($recording->getIncludeTimestamp());
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/service_id 1
     * @magentoConfigFixture default/mageproxy_connector/settings/run_mode manual
     * @magentoDataFixture   Magento/Store/_files/second_store.php
     */
    public function testStartMultiNewRecordings(): void
    {
        $postNewRecordingMock = $this->createMock(PostNewRecordingInterface::class);

        $storeManager = $this->objectManager->get(StoreManagerInterface::class);
        $defaultStoreId = Store::DISTRO_STORE_ID;
        $secondStoreId = (int) $storeManager->getStore('fixture_second_store')->getId();

        $responseFirst = $this->objectManager->create(PostNewRecordingResponseInterface::class);
        $uuidFirst = $this->objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $responseFirst->setId($uuidFirst);

        $responseSecond = $this->objectManager->create(PostNewRecordingResponseInterface::class);
        $uuidSecond = $this->objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $responseSecond->setId($uuidSecond);

        $responseThird = $this->objectManager->create(PostNewRecordingResponseInterface::class);
        $uuidThird = $this->objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $responseThird->setId($uuidThird);

        $postNewRecordingMock
            ->expects($this->exactly(3))
            ->method('execute')
            ->with(1)
            ->willReturn($responseFirst, $responseSecond, $responseThird);

        $this->objectManager->addSharedInstance($postNewRecordingMock, PostNewRecordingInterface::class, true);

        $sut = $this->objectManager->create(RecordingManagerInterface::class);
        $scheduledAtDt = (new \DateTime())->format('Y-m-d H:i:s');
        $sut->createNewRecordings([$defaultStoreId, $secondStoreId]);

        $recordingCollection = $this->objectManager->create(Collection::class);
        /** @var \Mageproxy\Connector\Model\Recording $recording */
        $recording = $recordingCollection->getItemsByColumnValue('store_id', $defaultStoreId)[0];
        self::assertSame($uuidFirst, $recording->getUuid());
        self::assertSame($defaultStoreId, $recording->getStoreId());
        self::assertSame($scheduledAtDt, $recording->getScheduledAt());
        self::assertSame(5, $recording->getDuration());
        self::assertFalse($recording->getIncludeTimestamp());

        /** @var \Mageproxy\Connector\Model\Recording $recording */
        $recording = $recordingCollection->getItemsByColumnValue('store_id', $secondStoreId)[0];
        self::assertSame($uuidSecond, $recording->getUuid());
        self::assertSame($secondStoreId, $recording->getStoreId());
        self::assertSame($scheduledAtDt, $recording->getScheduledAt());
        self::assertSame(5, $recording->getDuration());
        self::assertFalse($recording->getIncludeTimestamp());

        /** Trying to create another recording for same store may fail, just producing an error message */
        $sut->createNewRecordings([$secondStoreId]);

        $recordingCollection = $this->objectManager->create(Collection::class);
        /** @var \Mageproxy\Connector\Model\Recording $recording */
        $recording = $recordingCollection->getItemsByColumnValue('store_id', $secondStoreId)[0];
        self::assertNotEquals($uuidThird, $recording->getUuid());
        self::assertSame($secondStoreId, $recording->getStoreId());
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/service_id 1
     * @magentoConfigFixture default/mageproxy_connector/settings/run_mode manual
     * @magentoDataFixture   Magento/Store/_files/second_store.php
     */
    public function testStartAllStoresNewRecordings(): void
    {
        $postNewRecordingMock = $this->createMock(PostNewRecordingInterface::class);

        $storeManager = $this->objectManager->get(StoreManagerInterface::class);
        $defaultStoreId = Store::DISTRO_STORE_ID;
        $secondStoreId = (int) $storeManager->getStore('fixture_second_store')->getId();

        $responseFirst = $this->objectManager->create(PostNewRecordingResponseInterface::class);
        $uuidFirst = $this->objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $responseFirst->setId($uuidFirst);

        $responseSecond = $this->objectManager->create(PostNewRecordingResponseInterface::class);
        $uuidSecond = $this->objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $responseSecond->setId($uuidSecond);

        $responseThird = $this->objectManager->create(PostNewRecordingResponseInterface::class);
        $uuidThird = $this->objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $responseThird->setId($uuidThird);

        $postNewRecordingMock
            ->expects($this->exactly(3))
            ->method('execute')
            ->with(1)
            ->willReturn($responseFirst, $responseSecond, $responseThird);

        $this->objectManager->addSharedInstance($postNewRecordingMock, PostNewRecordingInterface::class, true);

        $sut = $this->objectManager->create(RecordingManagerInterface::class);
        $scheduledAtDt = (new \DateTime())->format('Y-m-d H:i:s');
        $sut->createNewRecordings([$defaultStoreId, $secondStoreId]);

        $recordingCollection = $this->objectManager->create(Collection::class);
        /** @var \Mageproxy\Connector\Model\Recording $recording */
        $recording = $recordingCollection->getItemsByColumnValue('store_id', $defaultStoreId)[0];
        self::assertSame($uuidFirst, $recording->getUuid());
        self::assertSame($defaultStoreId, $recording->getStoreId());
        self::assertSame($scheduledAtDt, $recording->getScheduledAt());
        self::assertSame(5, $recording->getDuration());
        self::assertFalse($recording->getIncludeTimestamp());

        /** @var \Mageproxy\Connector\Model\Recording $recording */
        $recording = $recordingCollection->getItemsByColumnValue('store_id', $secondStoreId)[0];
        self::assertSame($uuidSecond, $recording->getUuid());
        self::assertSame($secondStoreId, $recording->getStoreId());
        self::assertSame($scheduledAtDt, $recording->getScheduledAt());
        self::assertSame(5, $recording->getDuration());
        self::assertFalse($recording->getIncludeTimestamp());

        /** Trying to create another recording for same store may fail, just producing an error message */
        $sut->createNewRecordings([$secondStoreId]);

        $recordingCollection = $this->objectManager->create(Collection::class);
        /** @var \Mageproxy\Connector\Model\Recording $recording */
        $recording = $recordingCollection->getItemsByColumnValue('store_id', $secondStoreId)[0];
        self::assertNotEquals($uuidThird, $recording->getUuid());
        self::assertSame($secondStoreId, $recording->getStoreId());
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/service_id 1
     * @magentoConfigFixture default/mageproxy_connector/settings/run_mode manual
     * @magentoDataFixture   Magento/Store/_files/second_store.php
     */
    public function testStartAllStoresNewRecordingsWithRollback(): void
    {
        $postNewRecordingMock = $this->createMock(PostNewRecordingInterface::class);

        $storeManager = $this->objectManager->get(StoreManagerInterface::class);
        $defaultStoreId = Store::DISTRO_STORE_ID;
        $secondStoreId = (int) $storeManager->getStore('fixture_second_store')->getId();

        $responseFirst = $this->objectManager->create(PostNewRecordingResponseInterface::class);
        $uuidFirst = $this->objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $responseFirst->setId($uuidFirst);

        $responseSecond = $this->objectManager->create(PostNewRecordingResponseInterface::class);
        $uuidSecond = $this->objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $responseSecond->setId($uuidSecond);

        $postNewRecordingMock
            ->expects($this->exactly(2))
            ->method('execute')
            ->with(1)
            ->willReturn($responseFirst, $responseSecond);

        $this->objectManager->addSharedInstance($postNewRecordingMock, PostNewRecordingInterface::class, true);

        $sut = $this->objectManager->create(RecordingManagerInterface::class);
        $scheduledAtDt = (new \DateTime())->format('Y-m-d H:i:s');
        $duration = 5;
        $sut->createNewRecordings([$defaultStoreId], $duration, null, true);

        $recordingCollection = $this->objectManager->create(Collection::class);
        /** @var \Mageproxy\Connector\Model\Recording $recording */
        $recording = $recordingCollection->getItemsByColumnValue('store_id', $defaultStoreId)[0];
        self::assertSame($uuidFirst, $recording->getUuid());
        self::assertSame($defaultStoreId, $recording->getStoreId());
        self::assertSame($scheduledAtDt, $recording->getScheduledAt());
        self::assertSame($duration, $recording->getDuration());
        self::assertFalse($recording->getIncludeTimestamp());

        $this->expectException(LocalizedException::class);
        $duration = 5;
        $sut->createNewRecordings([$defaultStoreId, $secondStoreId], $duration, null, true);

        /** @var \Mageproxy\Connector\Model\Recording $recording */

        $recordingCollection = $this->objectManager->create(Collection::class);

        self::assertCount(0, $recordingCollection->getItemsByColumnValue('store_id', $secondStoreId));
    }

    /**
     * @magentoConfigFixture default_store mageproxy_connector/settings/run_mode auto
     */
    public function testItShouldRestartAccordingToTheRestartSchedule(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        $recording = $objectManager->create(RecordingInterface::class);

        $recording->setFinishedAt((new \DateTime())->modify('-6 minutes')->format('Y-m-d H:i:s'));
        $recording->setStoreId(Store::DISTRO_STORE_ID);
        $recording->setRecordSchedule([
            [
                'record_for' => 20,
                'record_time_unit' => 'm',
                'pause_for' => 5,
                'pause_time_unit' => 'm'
            ],
            [
                'record_for' => 10,
                'record_time_unit' => 'm',
                'pause_for' => 5,
                'pause_time_unit' => 'm'
            ],
            [
                'record_for' => 5,
                'record_time_unit' => 'm',
                'pause_for' => 5,
                'pause_time_unit' => 'm'
            ]
        ]);
        $recording->setStatus(RecordingInterface::STATUS_STOPPED);
        $recording->setStartCount(2);

        $recordingManager = $objectManager->get(RecordingManagerInterface::class);
        $result = $recordingManager->shouldRestart($recording);
        self::assertTrue($result);
    }

    public function testShouldNotRestartWhenItIsNotStopped(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $recording = $objectManager->create(RecordingInterface::class);
        $recording->setStatus(RecordingInterface::STATUS_RUNNING);
        $recordingManager = $objectManager->get(RecordingManagerInterface::class);
        $result = $recordingManager->shouldRestart($recording);
        self::assertFalse($result);
    }

    /**
     * @magentoConfigFixture default_store mageproxy_connector/settings/run_mode auto
     */
    public function testItShouldNotRestartWhenStillInPausedInterval(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        $recording = $objectManager->create(RecordingInterface::class);

        $finishedAtTs = (new \DateTime())->modify('-2 minutes');

        $recording->setFinishedAt($finishedAtTs->format('Y-m-d H:i:s'));
        $recording->setStartCount(2);
        $recording->setStoreId(Store::DISTRO_STORE_ID);
        $recording->setRecordSchedule([
            [
                'record_for' => 5,
                'record_time_unit' => 'm',
                'pause_for' => 5,
                'pause_time_unit' => 'm'
            ],
            [
                'record_for' => 5,
                'record_time_unit' => 'm',
                'pause_for' => 5,
                'pause_time_unit' => 'm'
            ]
        ]);
        $recording->setStatus(RecordingInterface::STATUS_STOPPED);
        $recording->setInitiator(RecordingInterface::INITIATOR_CRON);

        $recordingManager = $objectManager->get(RecordingManagerInterface::class);
        $result = $recordingManager->shouldRestart($recording);
        self::assertFalse($result);
    }
}
