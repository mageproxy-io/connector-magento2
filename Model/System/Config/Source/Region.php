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

class Region implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'us',
                'label' => __('United States')
            ],
            [
                'value' => 'eu',
                'label' => __('Europe')
            ],
            [
                'value' => 'ap',
                'label' => __('Asia Pacific')
            ],
            [
                'value' => 'au',
                'label' => __('Australia')
            ],
            [
                'value' => 'sa',
                'label' => __('South America')
            ],
            [
                'value' => 'af',
                'label' => __('Africa')
            ],
            [
                'value' => 'me',
                'label' => __('Middle East')
            ],
        ];
    }

    public function getLabel($regionCode): ?string
    {
        foreach ($this->toOptionArray() as ['value' => $code, 'label' => $label]) {
            if ($regionCode === (string) $code) {
                return (string) $label;
            }
        }
        return null;
    }
}
