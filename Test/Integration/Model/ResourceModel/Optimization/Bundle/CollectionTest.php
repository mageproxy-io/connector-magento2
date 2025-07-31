<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\ResourceModel\Optimization\Bundle;

use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Model\ResourceModel\Optimization\Bundle\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_deployed.php
     */
    public function testGetItems(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $collection = $objectManager->create(Collection::class);

        $optimization = $objectManager->get(OptimizationRepositoryInterface::class)->get('deployed');
        // Create two bundle objects and save them, then test they are returned
        // by the collection
        $bundle1 = $collection->getNewEmptyItem();
        $bundle1->setUrl('https://example.com/bundles/catalog_product_view.js');
        $bundle1->setSriHash('sri-hash1');
        $bundle1->setOptimizationId((int) $optimization->getId());
        $collection->addItem($bundle1);
        $bundle2 = $collection->getNewEmptyItem();
        $bundle2->setUrl('https://example.com/bundles/catalog_category_view.js');
        $bundle2->setSriHash('sri-hash2');
        $bundle2->setOptimizationId((int) $optimization->getId());
        $collection->addItem($bundle2);
        $collection->save();

        $collection = $objectManager->create(Collection::class);
        $items = $collection
            ->addFieldToFilter('optimization_id', $optimization->getId())
            ->getItems();
        $this->assertCount(2, $items);
    }
}
