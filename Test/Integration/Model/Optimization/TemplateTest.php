<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */

declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\Optimization;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\Data\OptimizationTemplateInterface;
use PHPUnit\Framework\TestCase;

class TemplateTest extends TestCase
{
    public function testUsesAnServiceContract(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $optimizationTemplate = $objectManager->create(OptimizationTemplateInterface::class);
    }

    public function testMethods(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $optimizationTemplate = $objectManager->create(OptimizationTemplateInterface::class);
        $optimizationTemplate->setMinifyJs(true);
        self::assertSame(true, $optimizationTemplate->getMinifyJs());
        $optimizationTemplate->setMinifyHtml(true);
        self::assertSame(true, $optimizationTemplate->getMinifyHtml());
        $optimizationTemplate->setExcludeDeps(['foo/bar']);
        self::assertSame(['foo/bar'], $optimizationTemplate->getExcludeDeps());
        $optimizationTemplate->setHandles(['catalog_product_view', 'cms_index_index']);
        self::assertSame(['catalog_product_view', 'cms_index_index'], $optimizationTemplate->getHandles());
        $optimizationTemplate->setStoreId(1);
        self::assertSame(1, $optimizationTemplate->getStoreId());
    }

    public function testDbSaveAndLoad(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $optimizationTemplate = $objectManager->create(OptimizationTemplateInterface::class);
        $optimizationTemplate->setMinifyJs(true);
        $optimizationTemplate->setMinifyHtml(true);
        $optimizationTemplate->setExcludeDeps(['foo/bar']);
        $optimizationTemplate->setHandles(['catalog_product_view', 'cms_index_index']);
        $optimizationTemplate->setStoreId(1);
        $optimizationTemplate->save();
        $optimizationTemplate->load($optimizationTemplate->getId());
        self::assertSame(true, $optimizationTemplate->getMinifyJs());
        self::assertSame(true, $optimizationTemplate->getMinifyHtml());
        self::assertSame(['foo/bar'], $optimizationTemplate->getExcludeDeps());
        self::assertSame(['catalog_product_view', 'cms_index_index'], $optimizationTemplate->getHandles());
        self::assertSame(1, $optimizationTemplate->getStoreId());
    }

    public function testEmptySerializedFields(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $optimizationTemplate = $objectManager->create(OptimizationTemplateInterface::class);
        $optimizationTemplate->setMinifyJs(true);
        $optimizationTemplate->setMinifyHtml(true);
        $optimizationTemplate->setStoreId(1);
        $optimizationTemplate->save();
        $optimizationTemplate->load($optimizationTemplate->getId());
        self::assertSame([], $optimizationTemplate->getExcludeDeps());
        self::assertSame([], $optimizationTemplate->getHandles());
    }

    public function testUniqueFields(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $optimizationTemplate = $objectManager->create(OptimizationTemplateInterface::class);
        $optimizationTemplate->setMinifyJs(true);
        $optimizationTemplate->setMinifyHtml(true);
        $optimizationTemplate->setStoreId(1);
        $optimizationTemplate->save();
        $optimizationTemplate = $objectManager->create(OptimizationTemplateInterface::class);
        $optimizationTemplate->setMinifyJs(false);
        $optimizationTemplate->setMinifyHtml(false);
        $optimizationTemplate->setStoreId(1);
        $this->expectException(AlreadyExistsException::class);
        $optimizationTemplate->save();
    }
}
