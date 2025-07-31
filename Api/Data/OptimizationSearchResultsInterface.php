<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface OptimizationSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Mageproxy\Connector\Api\Data\OptimizationInterface[]
     */
    public function getItems();

    /**
     * @param \Mageproxy\Connector\Api\Data\OptimizationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
