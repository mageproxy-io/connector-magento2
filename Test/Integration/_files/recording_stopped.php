<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

/**
 * Simulates a recording that was stopped
 */

use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use Mageproxy\Connector\Api\RecordingManagerInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;

Resolver::getInstance()->requireDataFixture('Mageproxy_Connector::Test/Integration/_files/recording_running.php');

/** @var \Magento\TestFramework\ObjectManager $objectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$recordingRepository = $objectManager->create(RecordingRepositoryInterface::class);
$recording = $recordingRepository->get('running');

$recording->setUuid('stopped');
$recordingManager = $objectManager->get(RecordingManagerInterface::class);
$recordingManager->stop($recording);
