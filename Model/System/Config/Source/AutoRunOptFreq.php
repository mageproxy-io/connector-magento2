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

class AutoRunOptFreq implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Continuous'),
                'value' => 1 * 60
            ],
            [
                'label' => __('Each 5 minutes'),
                'value' => 5 * 60
            ],
            [
                'label' => __('Each 10 minutes'),
                'value' => 10 * 60
            ],
            [
                'label' => __('Each 20 minutes'),
                'value' => 20 * 60
            ],
            [
                'label' => __('Each 30 minutes'),
                'value' => 30 * 60
            ],
            [
                'label' => __('Each 40 minutes'),
                'value' => 40 * 60
            ],
            [
                'label' => __('Each 50 minutes'),
                'value' => 50 * 60
            ],
            [
                'label' => __('Each hour'),
                'value' => 60 * 60
            ],
        ];
    }
}
