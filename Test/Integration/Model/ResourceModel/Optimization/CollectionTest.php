<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\ResourceModel\Optimization;

use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Model\ResourceModel\Optimization\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @var \Mageproxy\Connector\Model\ResourceModel\Optimization\Collection|null
     */
    private $collection;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->collection = $objectManager->create(Collection::class);
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimizations.php
     */
    public function testGetItemsReturnsCorrectSize(): void
    {
        $this->collection->load();
        self::assertCount(6, $this->collection->getItems());
    }

    public function testCollectionGetItemsReturnEmpty(): void
    {
        self::assertCount(0, $this->collection->getItems());
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimizations.php
     */
    public function testCollectionLoadsAssociatedRecordingObject(): void
    {
        foreach ($this->collection as $optimization) {
            self::assertNotNull($optimization->getRecording());
        }
    }
}
