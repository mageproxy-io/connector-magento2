<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\RequireJs;

use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Model\RequireJs\ConfigSerializer;

class ConfigSerializerTest
{
    public function testSerialize(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $serializer = $objectManager->get(ConfigSerializer::class);
        $serializer->serialize([]);
    }
}
