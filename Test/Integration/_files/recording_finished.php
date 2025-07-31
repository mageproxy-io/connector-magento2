<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;

Resolver::getInstance()
    ->requireDataFixture('Mageproxy_Connector::Test/Integration/_files/recording_running.php');

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$recordingRepository = $objectManager->create(RecordingRepositoryInterface::class);
$recording = $recordingRepository->get('running');

$recording->setStatus(RecordingInterface::STATUS_FINISHED);
$recording->setUuid('finished');

$duration = $recording->getDuration();
$durationPlus10 = $duration + 10;
$durationPlus5 = $duration + 5;
$recording->setScheduledAt(
    (new DateTime())
        ->modify("-{$durationPlus10} minutes")
        ->format('Y-m-d H:i:s')
);
$recording->setStartedAt(
    (new DateTime())
        ->modify("-{$durationPlus5} minutes")
        ->format('Y-m-d H:i:s'));

$recordingRepository->save($recording);
