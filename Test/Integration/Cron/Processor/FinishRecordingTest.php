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
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Cron\Processor\FinishRecording;
use PHPUnit\Framework\TestCase;

class FinishRecordingTest extends TestCase
{
    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_running.php
     * @magentoAppArea crontab
     */
    public function testItFinishesRunningRecording(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $finishRecordingCronProcessor = $objectManager->create(FinishRecording::class);
        $repository = $objectManager->create(RecordingRepositoryInterface::class);
        $recording = $repository->get('running');
        $finishRecordingCronProcessor->process($recording);
        $this->assertEquals(RecordingInterface::STATUS_FINISHED, $recording->getStatus());
    }
}
