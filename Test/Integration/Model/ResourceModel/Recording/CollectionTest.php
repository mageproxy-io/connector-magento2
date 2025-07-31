<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\ResourceModel\Recording;

use PHPUnit\Framework\TestCase;
use Mageproxy\Connector\Model\ResourceModel\Recording\Collection;

class CollectionTest extends TestCase
{
    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recordings.php
     * @magentoConfigFixture default/mageproxy_connector/settings/run_mode manual
     */
    public function testGetItems(): void
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $collection = $objectManager->create(Collection::class);
        $items = $collection->getItems();
        $this->assertNotEmpty($items);
    }
}
