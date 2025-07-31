<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\RequireJs\ConfigProvider;

use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Model\RequireJs\ConfigProvider\OptimizationDeployed;
use PHPUnit\Framework\TestCase;

class OptimizationDeployedTest extends TestCase
{
    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_deployed.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/bundles.php
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 1
     */
    public function testConfigPropertiesWhenOptimizationDeployedAndModuleEnabled(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $design = $objectManager->get(\Magento\Framework\View\DesignInterface::class);
        $design->setDesignTheme('Magento/blank', 'frontend');

        $assetRepo = $objectManager->get(\Magento\Framework\View\Asset\Repository::class);
        $staticContext = $assetRepo->getStaticViewFileContext();

        ['path' => $staticPath] = parse_url($staticContext->getBaseUrl());

        $expectedConfig = [
            'baseUrl' => 'https://static.mageproxy.io/f3822719-b48a-4829-bf15-a8cd0b2febc6/static/version1732969529/frontend/Magento/luma/en_US',
            'config' => [
                'mageproxy/requirejs-recorder' => [
                    'optimized' => true,
                    'minified' => true,
                    'origBaseUrl' => $staticContext->getBaseUrl() . $staticContext->getPath()
                ]
            ]
        ];

        $configProvider = $objectManager->get(OptimizationDeployed::class);
        $result = $configProvider->getConfig();
        self::assertEquals($expectedConfig, $result);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_deployed.php
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 0
     */
    public function testConfigPropertiesWhenOptimizationDeployedAndModuleDisabled(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $configProvider = $objectManager->get(OptimizationDeployed::class);
        $result = $configProvider->getConfig();

        self::assertEquals([], $result);
    }

}
