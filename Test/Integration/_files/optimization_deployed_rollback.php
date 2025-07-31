<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

use Magento\Framework\Exception\CouldNotDeleteException;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$repository = $objectManager->create(OptimizationRepositoryInterface::class);
$optimization = $repository->get('deployed');
try {
    $repository->delete($optimization);
} catch (CouldNotDeleteException $e) {
}
