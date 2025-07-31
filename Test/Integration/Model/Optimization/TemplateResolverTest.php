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

use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Model\Optimization\TemplateResolver;
use Monolog\Test\TestCase;

/**
 * @magentoDbIsolation enabled
 */
class TemplateResolverTest extends TestCase
{
    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_template_specific_store.php
     * @magentoConfigFixture default_store mageproxy_connector/settings/minify_html 1
     * @magentoConfigFixture default_store mageproxy_connector/settings/minify_js 0
     */
    public function testResolveReturnsSystemConfigValuesWhenNoTemplatesApply(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $recording = $objectManager->create(RecordingInterface::class);
        $resolver = $objectManager->create(TemplateResolver::class);
        $template = $resolver->resolve($recording);
        self::assertSame(false, $template->getMinifyJs());
        self::assertSame(true, $template->getMinifyHtml());
        self::assertNull($template->getBrowserslistConfig());
        self::assertEmpty($template->getExcludeDeps());
        self::assertEmpty($template->getHandles());
        self::assertEmpty($template->getTranspileGlobs());
        self::assertEmpty($template->getRemoveDeps());
        self::assertEmpty($template->getTemplateId());
    }

    /**
     * @magentoAppArea adminhtml
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_template_default_store.php
     */
    public function testResolveReturnsCorrectTemplateWithOnlyDefaultTemplate(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $recording = $objectManager->create(RecordingInterface::class);
        $recording->setStoreId(1);
        $resolver = $objectManager->create(TemplateResolver::class);
        $template = $resolver->resolve($recording);
        self::assertSame(false, $template->getMinifyJs());
        self::assertSame(false, $template->getMinifyHtml());
        self::assertSame('defaults', $template->getBrowserslistConfig());
        self::assertSame(['default/dep1', 'default/dep2'], $template->getExcludeDeps());
        self::assertSame(['default_handle1', 'default_handle2'], $template->getHandles());
        self::assertSame(['Foo_Bar/**/*.js'], $template->getTranspileGlobs());
        self::assertSame(['default/dep3', 'default/dep4'], $template->getRemoveDeps());
        self::assertSame(0, $template->getStoreId());
        self::assertNotEmpty($template->getTemplateId());
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_template_default_store.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_template_specific_store.php
     */
    public function testResolveReturnsTheStoreSpecificTemplate(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $recording = $objectManager->create(RecordingInterface::class);
        $recording->setStoreId(1);
        $resolver = $objectManager->create(TemplateResolver::class);
        $template = $resolver->resolve($recording);
        self::assertSame(true, $template->getMinifyJs());
        self::assertSame(true, $template->getMinifyHtml());
        self::assertSame(['store_handle1', 'store_handle2'], $template->getHandles());
        self::assertSame(['store/dep1', 'store/dep2'], $template->getExcludeDeps());
        self::assertNotEmpty($template->getTemplateId());
        self::assertSame(1, $template->getStoreId());
    }

}
