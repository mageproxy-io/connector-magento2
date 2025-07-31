<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Plugin\View\Asset;

use DOMDocument;
use DOMXPath;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\RequireJs\Config;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Result\PageFactory;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Mageproxy\Connector\Plugin\View\Asset\GroupedCollectionPlugin
 * @magentoAppIsolation enabled
 */
class GroupedCollectionPluginTest extends TestCase
{

    protected function setUp(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();
        $design = $objectManager->get(\Magento\Framework\View\DesignInterface::class);
        $design->setDesignTheme('Magento/luma');
        $configProvider = $objectManager->get(\Magento\Captcha\Model\Checkout\ConfigProvider::class);
        $objectManager->addSharedInstance($configProvider, \Magento\Checkout\Model\ConfigProviderInterface::class);
    }

    /**
     * @magentoAppArea frontend
     */
    public function testRecorderJsAssetAddedViaLayout(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $pageFactory= $objectManager->get(PageFactory::class);
        $page = $pageFactory->create(true);
        $page->addHandle(['default']);
        $page->getConfig()->setPageLayout('1column');
        $page->getConfig()->publicBuild();
        $assetCollection = $page->getConfig()->getAssetCollection();
        self::assertTrue($assetCollection->has('Mageproxy_Connector::js/requirejs/recorder.js'));
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 0
     */
    public function testRecorderJsAssetNotPresentWhenModuleDisabled(): void
    {
        $html = $this->renderPage();
        $scripts = $this->parseScripts($html);
        self::assertNotContains('Mageproxy_Connector/js/requirejs/recorder.js', $scripts);
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 1
     */
    public function testRecorderJsPresentWhenModuleEnabledAndNotRecording(): void
    {
        $html = $this->renderPage();
        $scripts = $this->parseScripts($html);
        self::assertContains('Mageproxy_Connector/js/requirejs/recorder.js', $scripts);
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 1
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_running.php
     */
    public function testRecorderJsAssetPresentWhileRecordingWithoutSriCache(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        // remove any left over sri cache
        $cache = $objectManager->get(CacheInterface::class);
        $cache->remove('INTEGRITY_frontend');

        $html = $this->renderPage();
        $scripts = $this->parseScripts($html);
        $this->assertScriptOrder($scripts);
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 1
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/sri_cache.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_running.php
     */
    public function testRecorderJsAssetPresentWhileRecordingWithSriCache(): void
    {
        if (!class_exists(\Magento\Csp\Model\SubresourceIntegrityRepository::class)) {
            $this->markTestSkipped('SRI implementation not available');
        }
        $html = $this->renderPage();
        $scripts = $this->parseScripts($html);
        $this->assertScriptOrder($scripts);
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 1
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_deployed.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/bundles.php
     */
    public function testAssetsRemovedWhenRecordingFinishedAndOptimizationDeployed(): void
    {
        $html = $this->renderPage();
        $scripts = $this->parseScripts($html);
        self::assertCount(1, $scripts);
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 1
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_running.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_deployed.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/bundles.php
     */
    public function testAssetsRemovedWhenRecordingInProgressAndOptimizationDeployed(): void
    {
        $html = $this->renderPage();
        $scripts = $this->parseScripts($html);
        self::assertCount(1, $scripts);
    }

    private function assertScriptOrder(array $scripts)
    {
        self::assertSame([
            Config::REQUIRE_JS_FILE_NAME,
            'Mageproxy_Connector/js/requirejs/recorder.js',
        ], array_slice($scripts, 0, 2));
    }

    private function renderPage(): string
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();
        $pageFactory= $objectManager->get(PageFactory::class);
        $page = $pageFactory->create(true);
        $page->addHandle(['default']);
        $page->getConfig()->setPageLayout('1column');
        $httpResponse = $objectManager->get(Http::class);
        $page->renderResult($httpResponse);
        return $httpResponse->getContent();
    }

    /**
     * Parse the src scripts from the HTML string provided
     */
    private function parseScripts(string $htmlString)
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true); // suppress HTML5 errors
        $dom->loadHTML($htmlString);
        libxml_clear_errors(); // clear errors for HTML5

        $xpath = new DOMXPath($dom);

        $nodes = $xpath->query("//script[@src]");

        $objectManager = Bootstrap::getObjectManager();
        $assetRepo = $objectManager->get(Repository::class);
        $staticContext = $assetRepo->getStaticViewFileContext();
        return array_map(function ($node) use ($staticContext) {
            $src = $node->getAttribute('src');
            $baseUrl = $staticContext->getBaseUrl();
            $path = $staticContext->getPath();
            $script = str_replace($baseUrl . $path, '', $src);
            return ltrim($script, '/');
        }, iterator_to_array($nodes));
    }
}
