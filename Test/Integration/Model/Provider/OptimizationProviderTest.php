<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\Provider;

use Magento\Framework\Api\SortOrder;
use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Model\Provider\OptimizationsProvider;
use Mageproxy\Connector\Model\Provider\SearchCriteriaProvider;
use PHPUnit\Framework\TestCase;

class OptimizationProviderTest extends TestCase
{
    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimizations.php
     */
    public function testGetItemsWithSingleStatus(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $searchCriteriaProvider = $objectManager->create(SearchCriteriaProvider::class, [
            'statuses' => [ OptimizationInterface::STATUS_DEPLOYED ]
        ]);
        $provider = $objectManager->create(OptimizationsProvider::class, [
            'searchCriteriaProvider' => $searchCriteriaProvider
        ]);
        $items = $provider->getItems();
        self::assertCount(1, $items);
        $optimization = array_shift($items);
        self::assertSame(OptimizationInterface::STATUS_DEPLOYED, $optimization->getStatus());
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimizations.php
     */
    public function testGetItemsWithMultipleStatusFilter(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $searchCriteriaProvider = $objectManager->create(SearchCriteriaProvider::class, [
            'statuses' => [
                OptimizationInterface::STATUS_DEPLOYED,
                OptimizationInterface::STATUS_FINISHED
            ]
        ]);
        $provider = $objectManager->create(OptimizationsProvider::class, [
            'searchCriteriaProvider' => $searchCriteriaProvider
        ]);
        $items = $provider->getItems();
        self::assertCount(2, $items);
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimizations.php
     */
    public function testSortOrder(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $searchCriteriaProvider = $objectManager->create(SearchCriteriaProvider::class, [
            'sortByField' => 'optimization_id',
            'sortByDirection' => SortOrder::SORT_DESC
        ]);
        $provider = $objectManager->create(OptimizationsProvider::class, [
            'searchCriteriaProvider' => $searchCriteriaProvider
        ]);
        $items = $provider->getItems();
        self::assertCount(6, $items);
        $optimization = array_shift($items);
        self::assertSame(5, $optimization->getStatus());
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimizations.php
     */
    public function testEmptyResult(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $searchCriteriaProvider = $objectManager->create(SearchCriteriaProvider::class, [
            'statuses' => [ 99 ]
        ]);
        $provider = $objectManager->create(OptimizationsProvider::class, [
            'searchCriteriaProvider' => $searchCriteriaProvider
        ]);
        $items = $provider->getItems();
        self::assertEmpty($items);
    }
}
