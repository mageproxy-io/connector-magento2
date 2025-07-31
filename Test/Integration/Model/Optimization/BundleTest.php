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
use Mageproxy\Connector\Api\Data\OptimizationBundleInterface;
use Mageproxy\Connector\Model\Optimization\Bundle;
use PHPUnit\Framework\TestCase;

/**
 * Model representing a JS bundle for a given optimization
 *
 * @covers \Mageproxy\Connector\Model\Optimization\Bundle
 */
class BundleTest extends TestCase
{
    /**
     * @var Bundle
     */
    private $bundleObject;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->bundleObject = $objectManager->create(Bundle::class);
    }

    public function testImplementsInterface(): void
    {
        $this->assertInstanceOf(OptimizationBundleInterface::class, $this->bundleObject);
    }

    /**
     * @covers \Mageproxy\Connector\Model\Optimization\Bundle::getUrl
     */
    public function testGetSetUrl(): void
    {
        $this->assertTrue(method_exists(Bundle::class, 'setUrl'));
        $this->assertTrue(method_exists(Bundle::class, 'getUrl'));
        $url = 'https://example.com/bundles/catalog_product_view.js';
        $this->bundleObject->setUrl($url);
        $this->assertSame($url, $this->bundleObject->getUrl());
    }

    /**
     * @covers \Mageproxy\Connector\Model\Optimization\Bundle::getSriHash
     */
    public function testGetSetSriHash(): void
    {
        $this->assertTrue(method_exists(Bundle::class, 'setSriHash'));
        $this->assertTrue(method_exists(Bundle::class, 'getSriHash'));
        $sriHash = 'sri-hash';
        $this->bundleObject->setSriHash($sriHash);
        $this->assertSame($sriHash, $this->bundleObject->getSriHash());
    }

    /**
     * @covers \Mageproxy\Connector\Model\Optimization\Bundle::getOptimizationId
     */
    public function testGetSetOptimizationId(): void
    {
        $this->assertTrue(method_exists(Bundle::class, 'setOptimizationId'));
        $this->assertTrue(method_exists(Bundle::class, 'getOptimizationId'));
        $optimizationId = 1;
        $this->bundleObject->setOptimizationId($optimizationId);
        $this->assertSame($optimizationId, $this->bundleObject->getOptimizationId());
    }

    public function testGetResourceName(): void
    {
        $this->assertNotEmpty($this->bundleObject->getResourceName());
    }
}
