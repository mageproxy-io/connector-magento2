<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Helper;

use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Helper\Url as UrlHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Mageproxy\Connector\Helper\Url
 */
class UrlTest extends TestCase
{
    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_ready.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/bundles.php
     */
    public function testGetOptimizedStaticBaseUrl(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $optimization = $objectManager->get(OptimizationRepositoryInterface::class)->get('ready');
        $urlHelper = $objectManager->get(UrlHelper::class);
        $baseUrl = $urlHelper->getStaticBaseUrl($optimization);
        $this->assertSame('https://static.mageproxy.io/f3822719-b48a-4829-bf15-a8cd0b2febc6/static/version1732969529/frontend/Magento/luma/en_US', $baseUrl);
    }

}
