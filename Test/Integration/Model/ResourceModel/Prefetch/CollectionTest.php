<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\ResourceModel\Prefetch;

use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Model\ResourceModel\Prefetch\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/prefetch.php
     */
    public function testGetItems(): void
    {
        $om = Bootstrap::getObjectManager();
        /** @var Collection $collection */
        $collection = $om->create(Collection::class);
        $items = $collection->getItems();
        self::assertNotEmpty($items);
        self::assertSame(1, count($items));
    }
}
