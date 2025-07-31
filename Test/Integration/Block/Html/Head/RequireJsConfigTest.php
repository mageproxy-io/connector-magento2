<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Block\Html\Head;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\Data\RequireJsConfigProviderInterface;
use Mageproxy\Connector\Api\OptimizationManagerInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Block\Html\Head\RequireJsConfig;
use Mageproxy\Connector\Model\RequireJs\CompositeConfigProvider;
use Mageproxy\Connector\Model\RequireJs\ConfigSerializer;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppIsolation enabled
 */
class RequireJsConfigTest extends TestCase
{
    /**
     * @magentoAppArea frontend
     * @magentoCache layout disabled
     */
    public function testItReplacesTheCoreRequireJsBlockInLayout(): void
    {
        $page = $this->createPage();

        $requireJsBlock = $page->getLayout()->getBlock('require.js');

        self::assertNotFalse($requireJsBlock);
        self::assertInstanceOf(RequireJsConfig::class, $requireJsBlock);
        // We are not replacing the template, just the block,  but make sure the original template isn't lost
        self::assertSame('Magento_Theme::page/js/require_js.phtml', $requireJsBlock->getTemplate());
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture default_store mageproxy_connector/settings/enabled 1
     * @magentoCache layout disabled
     */
    public function testChildConfigBlockPresentWhenModuleEnabled()
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $page = $this->createPage($objectManager);

        $requireJsBlock = $page->getLayout()->getBlock('require.js');
        self::assertNotFalse($requireJsBlock->getChildBlock('config'));
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture default_store mageproxy_connector/settings/enabled 0
     * @magentoCache layout disabled
     */
    public function testChildConfigBlockNotPresentWhenModuleDisabled(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $page = $this->createPage($objectManager);

        $requireJsBlock = $page->getLayout()->getBlock('require.js');
        self::assertFalse($requireJsBlock->getChildBlock('config'));
    }

    /**
     * @magentoAppArea frontend
     * @magentoCache layout disabled
     * @magentoConfigFixture default_store mageproxy_connector/settings/enabled 0
     */
    public function testItDoesNotChangeTheCoreBlockOutputWhenModuleDisabled(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $layout = $objectManager->create(LayoutInterface::class);

        $block = $layout->createBlock(Template::class);
        $block->setTemplate('Magento_Theme::page/js/require_js.phtml');
        $coreHtml = $block->toHtml();

        $page = $this->createPage();

        self::assertSame($block->toHtml(), $page->getLayout()->getBlock('require.js')->toHtml());

    }


    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture default_store mageproxy_connector/settings/enabled 1
     */
    public function testItMergesConfigProviderData(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $configProviderMockA = self::createMock(RequireJsConfigProviderInterface::class);
        $configProviderMockB = self::createMock(RequireJsConfigProviderInterface::class);

        $returnValueA = [
            'property1' => 'value1',
            'config' => [
                'property1' => 'value1',
                'property2' => 'value2',
                'property3' => 'value3'
            ]
        ];
        $configProviderMockA->expects(self::once())
            ->method('getConfig')
            ->willReturn($returnValueA);

        $returnValueB = [
            'property1' => 'valueX',
            'property2' => 'value2',
            'config' => [
                'property_new1' => 'value_new1',
                'property1' => 'valueX',
                'property2' => 'value2',
                'property3' => 'value3',
                'property_new2' => 'value_new2',
            ]
        ];
        $configProviderMockB->expects(self::once())
            ->method('getConfig')
            ->willReturn($returnValueB);

        $serializerMock = self::createMock(ConfigSerializer::class);
        $serializerMock->expects(self::once())
            ->method('serialize')
            ->with([
                'property1' => 'valueX',
                'property2' => 'value2',
                'config' => [
                    'property_new1' => 'value_new1',
                    'property1' => 'valueX',
                    'property2' => 'value2',
                    'property3' => 'value3',
                    'property_new2' => 'value_new2',
                ]
            ]);

        $layout = $objectManager->create(LayoutInterface::class);
        $configProvider = $objectManager->create(CompositeConfigProvider::class, [
            'configProviders' => [$configProviderMockA, $configProviderMockB]
        ]);
        $block = $objectManager->create(RequireJsConfig::class, [
            'configSerializer' => $serializerMock,
            'requireJsConfigProvider' => $configProvider
        ]);
        $layout->addBlock($block, 'require.js');
        $block->addChild('config', Template::class);
        $block->toHtml();
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture default/mageproxy_connector/settings/tracking_url http://example.com/v1/track
     * @magentoConfigFixture default_store mageproxy_connector/settings/enabled 1
     */
    public function testConfigWhenNotRecordingNorOptimizingWithModuleEnabled(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $page = $objectManager->get(PageFactory::class)->create();

        $configSerializerMock = self::createMock(ConfigSerializer::class);
        $configSerializerMock->expects(self::once())
            ->method('serialize');
        $objectManager->addSharedInstance($configSerializerMock, ConfigSerializer::class);

        $block = $page->getLayout()->getBlock('require.js');
        $block->toHtml();
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_running.php
     * @magentoConfigFixture default_store mageproxy_connector/settings/enabled 1
     * @magentoConfigFixture default/mageproxy_connector/settings/include_timestamp true
     * @magentoConfigFixture default/mageproxy_connector/settings/tracking_url http://example.com/v1/track
     */
    public function testConfigPropertiesWhenRecordingWhileNotOptimized(): void
    {
        // self::markTestIncomplete('This test is just kept for reference');
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $page = $this->createPage();

        $design = $objectManager->get(\Magento\Framework\View\DesignInterface::class);
        $design->setDesignTheme('Magento/blank', 'frontend');
        $staticContext = $objectManager->get(\Magento\Framework\View\Asset\Repository::class)
            ->getStaticViewFileContext();

        $request = $objectManager->get(\Magento\Framework\App\Request\Http::class);
        $request->setRouteName('cms')->setControllerName('index')->setActionName('index');

        $expectedConfig = [
            'baseUrl' => $staticContext->getBaseUrl() . $staticContext->getPath(),
            'config' => [
                'mageproxy/requirejs-recorder' => [
                    'trackUrl' => 'http://example.com/v1/track',
                    'pageHandle' => 'cms_index_index',
                    'includeTs' => true,
                    'nonAmd' => []
                ]
            ]
        ];

        $configSerializerMock = self::createMock(ConfigSerializer::class);
        $configSerializerMock->expects(self::once())
            ->method('serialize')
            ->with($expectedConfig);
        $objectManager->addSharedInstance($configSerializerMock, ConfigSerializer::class);

        $block = $page->getLayout()->getBlock('require.js');
        $block->toHtml();

    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_running.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_ready.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/bundles.php
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 1
     * @magentoConfigFixture default_store mageproxy_connector/settings/enabled 1
     * @magentoConfigFixture default/mageproxy_connector/settings/tracking_url http://example.com/v1/track
     */
    public function testConfigPropertiesWhileRecordingAndOptimizingAtSameTime(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $page = $this->createPage();
        $design = $objectManager->get(\Magento\Framework\View\DesignInterface::class);

        $design->setDesignTheme('Magento/blank', 'frontend');
        $staticContext = $objectManager->get(\Magento\Framework\View\Asset\Repository::class)
            ->getStaticViewFileContext();
        ['path' => $staticPath] = parse_url($staticContext->getBaseUrl());

        $request = $objectManager->get(\Magento\Framework\App\Request\Http::class);
        $request->setRouteName('cms')->setControllerName('index')->setActionName('index');

        $optimization = $objectManager->create(OptimizationRepositoryInterface::class)->get('ready');
        $optimization->setDepsCount(500);
        $optimization->setRequestedBy('user');
        $optimizationManager = $objectManager->get(OptimizationManagerInterface::class);
        $optimizationManager->deploy($optimization);

        $baseUrl = $staticContext->getBaseUrl() . $staticContext->getPath();

        $expectedConfig = [
            'baseUrl' => 'https://static.mageproxy.io/f3822719-b48a-4829-bf15-a8cd0b2febc6/static/version1732969529/frontend/Magento/luma/en_US',
            'config' => [
                'mageproxy/requirejs-recorder' => [
                    'trackUrl' => 'http://example.com/v1/track',
                    'pageHandle' => 'cms_index_index',
                    'includeTs' => false,
                    'nonAmd' => [
                        'requirejs-config' => $baseUrl . '/requirejs-config.js',
                        'requirejs/require' => $baseUrl . '/requirejs/require.js',
                        'Mageproxy_Connector/js/requirejs/recorder' => $baseUrl . '/Mageproxy_Connector/js/requirejs/recorder.js',
                        'mage/requirejs/mixins' => $baseUrl . '/mage/requirejs/mixins.js'
                    ],
                    'optimized' => true,
                    'minified' => true,
                    'origBaseUrl' => $staticContext->getBaseUrl() . $staticContext->getPath(),
                    'sriHashes' => [
                        'https://static.mageproxy.io/f3822719-b48a-4829-bf15-a8cd0b2febc6/static/version1732969529/frontend/Magento/luma/en_US/bundles/core.min.js' => 'sha384-M1wK9tbqXuyp4dQPkyZhoGp00hN9mFEm8hq4HsA1RRidGbGqbWPSRR3M1vVUMnLy',
                        'https://static.mageproxy.io/f3822719-b48a-4829-bf15-a8cd0b2febc6/static/version1732969529/frontend/Magento/luma/en_US/bundles/common' => 'sha384-M1wK9tbqXuyp4dQPkyZhoGp00hN9mFEm8hq4HsA1RRidGbGqbWPSRR3M1vVUMnLy',
                        'https://static.mageproxy.io/f3822719-b48a-4829-bf15-a8cd0b2febc6/static/version1732969529/frontend/Magento/luma/en_US/bundles/cms_index_index.min.js' => 'sha384-M1wK9tbqXuyp4dQPkyZhoGp00hN9mFEm8hq4HsA1RRidGbGqbWPSRR3M1vVUMnLy',
                        'https://static.mageproxy.io/f3822719-b48a-4829-bf15-a8cd0b2febc6/static/version1732969529/frontend/Magento/luma/en_US/bundles/catalog_product_view.min.js' => 'sha384-M1wK9tbqXuyp4dQPkyZhoGp00hN9mFEm8hq4HsA1RRidGbGqbWPSRR3M1vVUMnLy',
                        'https://static.mageproxy.io/f3822719-b48a-4829-bf15-a8cd0b2febc6/static/version1732969529/frontend/Magento/luma/en_US/bundles/checkout_cart_index.min.js' => 'sha384-M1wK9tbqXuyp4dQPkyZhoGp00hN9mFEm8hq4HsA1RRidGbGqbWPSRR3M1vVUMnLy',
                        'https://static.mageproxy.io/f3822719-b48a-4829-bf15-a8cd0b2febc6/static/version1732969529/frontend/Magento/luma/en_US/bundles/checkout_index_index.min.js' => 'sha384-M1wK9tbqXuyp4dQPkyZhoGp00hN9mFEm8hq4HsA1RRidGbGqbWPSRR3M1vVUMnLy',
                        'https://static.mageproxy.io/f3822719-b48a-4829-bf15-a8cd0b2febc6/static/version1732969529/frontend/Magento/luma/en_US/bundles/cms_page_view.min.js' => 'sha384-M1wK9tbqXuyp4dQPkyZhoGp00hN9mFEm8hq4HsA1RRidGbGqbWPSRR3M1vVUMnLy'
                    ]
                ]
            ]
        ];

        $configSerializerMock = self::createMock(ConfigSerializer::class);
        $configSerializerMock->expects(self::once())
            ->method('serialize')
            ->with($expectedConfig);
        $objectManager->addSharedInstance($configSerializerMock, ConfigSerializer::class);

        $block = $page->getLayout()->getBlock('require.js');
        $block->toHtml();
    }

    private function createPage(): Page
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();
        $page = $objectManager->create(PageFactory::class)->create(true);
        $page->addHandle(['default']);
        $page->getConfig()->setPageLayout('1column');
        return $page;
    }

}
