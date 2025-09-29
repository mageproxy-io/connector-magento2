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

use Magento\Framework\Data\OptionSourceInterface;

class PrefetchOn implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'interaction', 'label' => __('Interaction')],
            ['value' => 'viewport', 'label' => __('Viewport')],
        ];
    }
}

