<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */

declare(strict_types=1);

namespace Mageproxy\Connector\Model\Optimization\Source;

use Magento\Framework\Data\OptionSourceInterface;

class TemplateStoreSelection implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            [
                'value' => 0,
                'label' => __('All Stores')
            ],
            [
                'value' => 1,
                'label' => __('Current Store')
            ],
        ];
    }
}
