<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

use Magento\Framework\Exception\CouldNotDeleteException;
use Mageproxy\Connector\Api\OptimizationTemplateRepositoryInterface;

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$repository = $objectManager->create(OptimizationTemplateRepositoryInterface::class);
$searchCriteriaBuilder = $objectManager->create(\Magento\Framework\Api\SearchCriteriaBuilder::class);
$searchCriteria = $searchCriteriaBuilder->addFilter('store_id', Magento\Store\Model\Store::DEFAULT_STORE_ID)->create();
$result = $repository->getList($searchCriteria);
if ($result->getTotalCount() === 0) {
    return;
}
$items = $result->getItems();
$template = array_shift($items);
try {
    $repository->delete($template);
} catch (CouldNotDeleteException $e) {
}
