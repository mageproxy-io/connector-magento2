<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

use Magento\Framework\RequireJs\Config;

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$state = $objectManager->get(\Magento\Framework\App\State::class);
$state->setAreaCode('frontend');
$design = $objectManager->get(\Magento\Framework\View\DesignInterface::class);
$design->setDesignTheme('Magento/luma');
$cache = $objectManager->get(\Magento\Framework\App\CacheInterface::class);
$assetRepo = $objectManager->get(\Magento\Framework\View\Asset\Repository::class);
$staticContext = $assetRepo->getStaticViewFileContext();

$path = $staticContext->getPath();

$payload = [
    $path . '/' . Config::REQUIRE_JS_FILE_NAME => hash('sha256', Config::REQUIRE_JS_FILE_NAME),
    $path . '/' . Config::MIXINS_FILE_NAME => hash('sha256', Config::MIXINS_FILE_NAME),
    $path . '/' . Config::CONFIG_FILE_NAME => hash('sha256', Config::CONFIG_FILE_NAME),
    $path . '/Mageproxy_Connector/js/requirejs/recorder.js' => hash('sha256', 'recorder.js'),
];
$cacheKey = 'INTEGRITY_frontend';
$cache->remove($cacheKey);
$cache->save(json_encode($payload), $cacheKey);
