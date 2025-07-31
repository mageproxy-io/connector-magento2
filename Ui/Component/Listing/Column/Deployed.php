<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;

class Deployed implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Yes'),
                'value' => '1'
            ],
            [
                'label' => '',
                'value' => '0'
            ]
        ];
    }
}
