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
use Mageproxy\Connector\Api\OptimizationManagerInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;

Resolver::getInstance()->requireDataFixture('Mageproxy_Connector::Test/Integration/_files/optimization_ready.php');

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$optimizationRepository = $objectManager->get(OptimizationRepositoryInterface::class);
$optimization = $optimizationRepository->get('ready');

$optimizationManager = $objectManager->get(OptimizationManagerInterface::class);
$optimizationManager->deploy($optimization);
$optimization->setUuid('deployed');
$optimizationRepository->save($optimization);
