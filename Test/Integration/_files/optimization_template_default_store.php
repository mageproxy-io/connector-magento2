<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */

declare(strict_types=1);

use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\Data\OptimizationTemplateInterfaceFactory;
use Mageproxy\Connector\Api\OptimizationTemplateRepositoryInterface;

$objectManager = Bootstrap::getObjectManager();

$templateFactory = $objectManager->get(OptimizationTemplateInterfaceFactory::class);
$templateRepository = $objectManager->get(OptimizationTemplateRepositoryInterface::class);
$template = $templateFactory->create();
$template->setMinifyHtml(false);
$template->setMinifyJs(false);
$template->setExcludeDeps(['default/dep1', 'default/dep2']);
$template->setRemoveDeps(['default/dep3', 'default/dep4']);
$template->setBrowserslistConfig('defaults');
$template->setTranspileGlobs(['Foo_Bar/**/*.js']);
$template->setHandles(['default_handle1', 'default_handle2']);
$template->setStoreId(Magento\Store\Model\Store::DEFAULT_STORE_ID);
$templateRepository->save($template);
