<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

use Mageproxy\Connector\Api\RecordingRepositoryInterface;

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$repository = $objectManager->create(RecordingRepositoryInterface::class);
try {
    $recording = $repository->get('pending');
    $repository->delete($recording);
} catch (\Exception $e) {
}
