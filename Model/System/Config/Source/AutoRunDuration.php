<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\System\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class AutoRunDuration implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('1 hour'),
                'value' => 1 * 60
            ],
            [
                'label' => __('2 hours'),
                'value' => 2 * 60
            ],
            [
                'label' => __('4 hours'),
                'value' => 4 * 60
            ],
            [
                'label' => __('8 hours'),
                'value' => 8 * 60
            ],
            [
                'label' => __('16 hours'),
                'value' => 16 * 60
            ],
            [
                'label' => __('1 day'),
                'value' => 24 * 60
            ],
            [
                'label' => __('2 days'),
                'value' => 48 * 60
            ],
            [
                'label' => __('3 days'),
                'value' => 72 * 60
            ],
        ];
    }
}
