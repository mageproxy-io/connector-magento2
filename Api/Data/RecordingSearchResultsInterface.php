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

interface RecordingSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get list of recordings
     *
     * @return \Mageproxy\Connector\Model\Recording[]
     */
    public function getItems();

    /**
     * Set list of recordings
     *
     * @param \Mageproxy\Connector\Model\Recording[] $items
     * @return $this
     */
    public function setItems(array $items);
}
