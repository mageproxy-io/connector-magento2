<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */

declare(strict_types=1);

namespace Mageproxy\Connector\Model\Config\Source;

class No implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [['value' => 0, 'label' => __('No')]];
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [0 => __('No')];
    }
}
