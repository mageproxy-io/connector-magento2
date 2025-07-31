<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

use Magento\Csp\Model\SubresourceIntegrity\HashGenerator;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use Mageproxy\Connector\Model\ResourceModel\Optimization\Bundle\Collection as OptimizationBundleCollection;

/**
 * An optimization that is ready to deploy will have optimization bundles associated with it
 */

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

Resolver::getInstance()->requireDataFixture('Mageproxy_Connector::Test/Integration/_files/optimization_ready.php');

$collection = $objectManager->create(\Mageproxy\Connector\Model\ResourceModel\Optimization\Collection::class);
$optimization = $collection->getFirstItem();

$sriHashGenerator = $objectManager->get(HashGenerator::class);

$optimizationBundleCollection = $objectManager->create(OptimizationBundleCollection::class);
foreach ([
    'bundles/core.min.js',
    'bundles/common',
    'bundles/cms_index_index.min.js',
    'bundles/catalog_product_view.min.js',
    'bundles/checkout_cart_index.min.js',
    'bundles/checkout_index_index.min.js',
    'bundles/cms_page_view.min.js',
 ] as $bundle) {
    /** @var \Mageproxy\Connector\Model\Optimization\Bundle $optimizationBundle */
    $optimizationBundle = $optimizationBundleCollection->getNewEmptyItem();
    $optimizationBundle->setOptimization($optimization);
    $optimizationBundle->setUrl('https://static.mageproxy.io/f3822719-b48a-4829-bf15-a8cd0b2febc6/static/version1732969529/frontend/Magento/luma/en_US/' . $bundle);
    $optimizationBundle->setSriHash('sha384-M1wK9tbqXuyp4dQPkyZhoGp00hN9mFEm8hq4HsA1RRidGbGqbWPSRR3M1vVUMnLy');
    $optimizationBundle->setRawSize(5000);
    $optimizationBundle->setMinifiedSize(3000);
    $optimizationBundle->setCompressedSize(2000);
    $optimizationBundleCollection->addItem($optimizationBundle);
}
$optimizationBundleCollection->save();
