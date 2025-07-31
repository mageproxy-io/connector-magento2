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
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Model\Optimization\Bundle as BundleModel;
use Mageproxy\Connector\Model\ResourceModel\Optimization\Bundle;
use PHPUnit\Framework\TestCase;

class BundleTest extends TestCase
{
    public function testResourceMainTableAndIdValues(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $resourceModel = $objectManager->create(Bundle::class);
        $this->assertSame('mageproxy_optimization_bundle', $resourceModel->getMainTable());
        $this->assertSame('bundle_id', $resourceModel->getIdFieldName());
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_deployed.php
     */
    public function testPersistence(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $optimization = $objectManager->get(OptimizationRepositoryInterface::class)->get('deployed');
        $resourceModel = $objectManager->create(Bundle::class);
        $bundleModel = $objectManager->create(BundleModel::class);
        $bundleModel->setUrl('https://example.com/bundles/catalog_product_view.js');
        $bundleModel->setSriHash('sri-hash');
        $bundleModel->setOptimization($optimization);
        $resourceModel->save($bundleModel);
        $this->assertNotNull($bundleModel->getId());
    }

}
