<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration;

use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Model\ResourceModel\Recording\Collection;
use PHPUnit\Framework\TestCase;

class FixturesTest extends TestCase
{

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_pending.php
     */
    public function testRecordingPending(): void
    {
        $this->assertStatus('pending', RecordingInterface::STATUS_PENDING);
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_running.php
     */
    public function testRecordingRunning(): void
    {
        $this->assertStatus('running', RecordingInterface::STATUS_RUNNING);
        $recording = $this->getRecording('running');
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     */
    public function testRecordingFinished(): void
    {
        $this->assertStatus('finished', RecordingInterface::STATUS_FINISHED);
    }

    private function getRecording(string $uuid)
    {
        $objectManager = Bootstrap::getObjectManager();
        $repository = $objectManager->create(RecordingRepositoryInterface::class);
        $recording = $repository->get($uuid);
        self::assertNotEmpty($recording->getId());
        return $recording;
    }

    private function assertStatus(string $uuid, int $status): void
    {
        $recording = $this->getRecording($uuid);
        self::assertSame($status, $recording->getStatus());
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_requested.php
     */
    public function testRequestedOptimization(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $optimization = $objectManager->get(OptimizationRepositoryInterface::class)->get('requested');
        self::assertNotEmpty($optimization->getId());
        self::assertNotEmpty($optimization->getStoreId());
        $recording = $optimization->getRecording();
        self::assertNotEmpty($recording->getId());
        self::assertSame($recording->getStoreId(), $optimization->getStoreId());
        self::assertSame(OptimizationInterface::STATUS_REQUESTED, $optimization->getStatus());
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_ready.php
     */
    public function testReadyOptimization(): void
    {
        $optimization = Bootstrap::getObjectManager()
            ->get(OptimizationRepositoryInterface::class)
            ->get('ready');
        self::assertNotEmpty($optimization->getId());
        self::assertSame(OptimizationInterface::STATUS_READY, $optimization->getStatus());
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_deployed.php
     */
    public function testDeployedOptimization(): void
    {
        $optimization = Bootstrap::getObjectManager()
            ->get(OptimizationRepositoryInterface::class)
            ->get('deployed');
        self::assertNotEmpty($optimization->getId());
        self::assertSame(OptimizationInterface::STATUS_DEPLOYED, $optimization->getStatus());
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recordings.php
     * @magentoConfigFixture default/mageproxy_connector/settings/run_mode manual
     */
    public function testRecordingsFixture(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $collection = $objectManager->create(Collection::class);
        $items = $collection->getItems();
        self::assertCount(6, $items);
    }
}
