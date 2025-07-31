<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$cache = $objectManager->get(\Magento\Framework\App\CacheInterface::class);

$cacheKey = 'INTEGRITY_frontend';
$cache->remove($cacheKey);
