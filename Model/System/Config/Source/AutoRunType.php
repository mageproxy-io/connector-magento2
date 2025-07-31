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

class AutoRunType implements OptionSourceInterface
{
    public const SCHEDULED = 'scheduled';
    public const CONTINUOUS = 'continuous';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Scheduled'),
                'value' => self::SCHEDULED
            ],
            [
                'label' => __('Continuous'),
                'value' => self::CONTINUOUS
            ]
        ];
    }
}
