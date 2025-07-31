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
use Mageproxy\Connector\Model\RequireJs\ConfigProvider\BaseUrl;
use PHPUnit\Framework\TestCase;

class BaseUrlTest extends TestCase
{
    public function testGetConfigResult(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $baseUrlConfigProvider = $objectManager->get(BaseUrl::class);

        $config = $baseUrlConfigProvider->getConfig();

        self::assertArrayHasKey('baseUrl', $config);
        self::assertNotEmpty($config['baseUrl']);
    }
}
