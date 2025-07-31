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

use Magento\Framework\View\Asset\AssetInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Model\Asset\RecorderJsAssetFactory;
use PHPUnit\Framework\TestCase;

class RecorderJsAssetFactoryTest extends TestCase
{
    /**
     * @magentoAppArea frontend
     */
    public function testFactory(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $factory = $objectManager->get(RecorderJsAssetFactory::class);
        $asset = $factory->create();
        self::assertInstanceOf(AssetInterface::class, $asset);
        self::assertSame('Mageproxy_Connector', $asset->getModule());
        self::assertSame('js', $asset->getContentType());
        self::assertSame('js/requirejs/recorder.js', $asset->getFilePath());
    }
}
