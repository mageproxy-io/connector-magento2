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

use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Model\RequireJs\ConfigProvider\SriHashes;
use PHPUnit\Framework\TestCase;

class SriHashesTest extends TestCase
{
    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_deployed.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/bundles.php
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 1
     */
    public function testConfigPropertiesWhenOptimizationDeployedWithSri(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $layoutMock = self::createMock(LayoutInterface::class);

        $sriBlockMock = self::getMockBuilder(\StdClass::class)
            ->addMethods(['getSerialized'])
            ->getMock();
        $sriBlockMock->expects(self::once())
            ->method('getSerialized')
            ->willReturn('{"https://www.example.com/static/version123123/Magento/luma/en_US/jquery.js":"sha256-1234"}');

        $layoutMock->expects(self::once())
            ->method('getBlock')
            ->with('csp.sri.hashes')
            ->willReturn($sriBlockMock);

        $configProvider = $objectManager->create(SriHashes::class, [
            'layout' => $layoutMock
        ]);

        $result = $configProvider->getConfig();

        self::assertNotNull($result['config']['mageproxy/requirejs-recorder']['sriHashes']);
        self::assertIsArray($result['config']['mageproxy/requirejs-recorder']['sriHashes']);
        self::assertCount(8, $result['config']['mageproxy/requirejs-recorder']['sriHashes']);
    }
}
