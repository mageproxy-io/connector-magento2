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

interface OptimizationTemplateSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \Mageproxy\Connector\Api\Data\OptimizationTemplateInterface[]
     */
    public function getItems();

    /**
     * @param \Mageproxy\Connector\Api\Data\OptimizationTemplateInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

}
