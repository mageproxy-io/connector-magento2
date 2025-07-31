<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\Asset;

use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\DesignInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Model\Asset\AssetRegistry;
use PHPUnit\Framework\TestCase;

class AssetRegistryTest extends TestCase
{
    /**
     * @magentoAppArea frontend
     * @covers \Mageproxy\Connector\Model\Asset\NonAmdAssetCollection::m
     */
    public function testRegister(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        // Set design theme to Magento/blank
        $design = $objectManager->get(DesignInterface::class);
        $design->setDesignTheme('Magento/blank', 'frontend');

        $registry = $objectManager->create(AssetRegistry::class, [
            'matchAssets' => [
                'foo/bar' => 'foo/bar.js',
                'Magento_Catalog/js/foo/baz' => 'Magento_Catalog/js/foo/baz.js'
            ]
        ]);

        $assetRepo = $objectManager->get(Repository::class);
        $assetExpectMatched = $assetRepo->createAsset('foo/bar.js');
        $assetExpectNotMatched = $assetRepo->createAsset('foo/bar2.js');
        $assetWithModuleExpectMatched = $assetRepo->createAsset('Magento_Catalog::js/foo/baz.js');

        self::assertTrue($registry->register($assetExpectMatched));
        self::assertFalse($registry->register($assetExpectNotMatched));
        self::assertTrue($registry->register($assetWithModuleExpectMatched));

        self::assertContains($assetExpectMatched, array_values($registry->registry()));
        self::assertContains($assetWithModuleExpectMatched, array_values($registry->registry()));
        self::assertEquals(2, count($registry->registry()));
    }

    /**
     * @magentoAppArea frontend
     */
    public function testRegisterForVirtualType(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $registry = $objectManager->get('NonAmdAssetsRegistry');
        foreach ([
            'requirejs/require.js',
            'mage/requirejs/mixins.js',
            'requirejs-config.js',
            'Mageproxy_Connector::js/requirejs/recorder.js'
         ] as $identifier) {
            $asset = $objectManager->get(Repository::class)->createAsset($identifier);
            $registry->register($asset);
        }
        self::assertCount(4, $registry->registry());
        self::assertContains('requirejs/require', array_keys($registry->registry()));
        self::assertContains('mage/requirejs/mixins', array_keys($registry->registry()));
        self::assertContains('requirejs-config', array_keys($registry->registry()));
        self::assertContains('Mageproxy_Connector/js/requirejs/recorder', array_keys($registry->registry()));
    }

    /**
     * @magentoAppArea frontend
     */
    public function testHas()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $registry = $objectManager->get('NonAmdAssetsRegistry');
        $assetRepo = $objectManager->get(Repository::class);
        $requireJsAsset = $assetRepo->createAsset('requirejs/require.js');
        $registry->register($requireJsAsset);
        self::assertTrue($registry->has($requireJsAsset));
    }
}
