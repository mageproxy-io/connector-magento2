<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
/**
 * Fixture: create a single prefetch definition
 */

use Magento\TestFramework\Helper\Bootstrap;

/** @var \Magento\TestFramework\ObjectManager $objectManager */
$objectManager = Bootstrap::getObjectManager();

/** @var \Mageproxy\Connector\Model\PrefetchFactory $factory */
$factory = $objectManager->get(\Mageproxy\Connector\Model\PrefetchFactory::class);
/** @var \Mageproxy\Connector\Model\ResourceModel\Prefetch $resource */
$resource = $objectManager->get(\Mageproxy\Connector\Model\ResourceModel\Prefetch::class);

$model = $factory->create();
$model->setData('store_id', 1);
$model->setData('rules', [
    ['selector' => 'a', 'bundle_pattern' => 'bundle-a', 'prefetch_on' => 'interaction'],
    ['selector' => 'b', 'bundle_pattern' => 'bundle-b', 'prefetch_on' => 'viewport'],
]);
$resource->save($model);
