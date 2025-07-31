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

$templateRepository = $objectManager->get(OptimizationTemplateRepositoryInterface::class);

$templateFactory = $objectManager->get(OptimizationTemplateInterfaceFactory::class);
$template = $templateFactory->create();
$template->setMinifyHtml(true);
$template->setMinifyJs(true);
$template->setExcludeDeps(['store/dep1', 'store/dep2']);
$template->setHandles(['store_handle1', 'store_handle2']);
$template->setStoreId(Magento\Store\Model\Store::DISTRO_STORE_ID);

$templateRepository->save($template);
