<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */

declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Cron\Processor;

use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\OptimizationManagerInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Api\RecordingManagerInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Cron\Processor\AutoRun\RequestOptimization;
use Mageproxy\Connector\Model\ApiClient\GetRecordingSnapshotInterface;
use Mageproxy\Connector\Model\ApiClient\GetRecordingSnapshotResponseInterface;
use Mageproxy\Connector\Model\ProviderInterface;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea crontab
 */
class AutoRunTest extends TestCase
{
    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_running.php
     * @magentoConfigFixture default/mageproxy_connector/general/run_mode manual
     */
    public function testItDoesNotRequestAnOptimizationWhenAutoModeInactive(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        $optimizationManagerMock = self::createMock(OptimizationManagerInterface::class);
        $optimizationManagerMock->expects(self::never())->method('request');

        $recording = $objectManager->get(RecordingRepositoryInterface::class)->get('running');
        $recording->setInitiator(RecordingInterface::INITIATOR_CRON);

        $providerMock = self::createMock(ProviderInterface::class);

        $recordingManager = $objectManager->create(
            RecordingManagerInterface::class,
            [
                'getRecordingSnapshotApiClient' => $this->createRecordingSnapShotMock(
                    '123e4567-e89b-12d3-a456-426614174000',
                    '9cdeb9d0f43cec81cc80654973e56c94904aeff495fe008c608d57f3d99d59fc',
                    0,
                    0
                )
            ]
        );

        $sut = $objectManager->create(RequestOptimization::class, [
            'optimizationManager' => $optimizationManagerMock,
            'provider' => $providerMock,
            'recordingManager' => $recordingManager
        ]);

        $sut->process($recording);
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_stopped.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_requested.php
     */
    public function testItDoesNotRequestAnOptimizationWhenRecordingHadFinalOptimization(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $optimizationManagerMock = self::createMock(OptimizationManagerInterface::class);
        $optimizationManagerMock->expects(self::never())->method('request');

        $providerMock = self::createMock(ProviderInterface::class);

        $getRecordingSnapshotMock = self::createMock(GetRecordingSnapshotInterface::class);
        $getRecordingSnapshotMock->expects(self::once())
            ->method('execute');

        $recordingManager = $objectManager->create(
            RecordingManagerInterface::class,
            [
                'getRecordingSnapshotApiClient' => $getRecordingSnapshotMock
            ]
        );

        $sut = $objectManager->create(RequestOptimization::class, [
            'optimizationManager' => $optimizationManagerMock,
            'provider' => $providerMock,
            'recordingManager' => $recordingManager
        ]);

        $recording = $objectManager->get(RecordingRepositoryInterface::class)->get('stopped');
        $optimization = $objectManager->get(OptimizationRepositoryInterface::class)->get('requested');
        $optimization->setRequestedAt((new \DateTime())->format('Y-m-d H:i:s'));
        $optimization->save();
        $recording->setFinishedAt((new \DateTime())->modify('-5 minutes')->format('Y-m-d H:i:s'));
        $recording->save();
        $recording->setRecordSchedule([
            [
                'record_for' => '5',
                'record_time_unit' => 'm',
                'pause_for' => '10',
                'pause_time_unit' => 'm',
            ],
            [
                'record_for' => '5',
                'record_time_unit' => 'm',
                'pause_for' => '10',
                'pause_time_unit' => 'm',
            ],
        ]);
        $recording->setStartCount(2);
        $recording->setInitiator(RecordingInterface::INITIATOR_CRON);
        $sut->process($recording);
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_running.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_deployed.php
     */
    public function testItDoesNotRequestAnOptimizationWhenRecordingSnapshotUnchanged(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        $recording = $objectManager->get(RecordingRepositoryInterface::class)->get('running');
        $recording->setInitiator(RecordingInterface::INITIATOR_CRON);

        $optimizationManagerMock = self::createMock(OptimizationManagerInterface::class);
        $optimizationManagerMock->expects(self::never())->method('request');

        $providerMock = self::createMock(ProviderInterface::class);

        // matches the checksum on the deployed optimization (see fixture)
        $checksum = '9cdeb9d0f43cec81cc80654973e56c94904aeff495fe008c608d57f3d99d59fc';

        $recordingManager = $objectManager->create(
            RecordingManagerInterface::class,
            [
                'getRecordingSnapshotApiClient' => $this->createRecordingSnapShotMock(
                    $recording->getUuid(),
                    $checksum,
                    0,
                    0
                )
            ]
        );

        $sut = $objectManager->create(RequestOptimization::class, [
            'optimizationManager' => $optimizationManagerMock,
            'provider' => $providerMock,
            'recordingManager' => $recordingManager
        ]);

        $sut->process($recording);

    }

    private function createRecordingSnapShotMock(
        string $uuid,
        string $checksum,
        int $depsCnt,
        int $hdlsCnt
    ) {

        $objectManager = Bootstrap::getObjectManager();

        $response = $objectManager->create(GetRecordingSnapshotResponseInterface::class);
        $response->setChecksum($checksum);
        $response->setId($uuid);
        $response->setHdlsCnt($hdlsCnt);
        $response->setDepsCnt($depsCnt);
        $getRecordingSnapshotMock = self::createMock(GetRecordingSnapshotInterface::class);
        $getRecordingSnapshotMock->expects(self::once())
            ->method('execute')
            ->willReturn($response);
        return $getRecordingSnapshotMock;
    }
}
