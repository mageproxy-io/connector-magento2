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

class RunMode implements OptionSourceInterface
{
    public const MODE_AUTO = 'auto';
    public const MODE_MANUAL = 'manual';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Auto'),
                'value' => self::MODE_AUTO
            ],
            [
                'label' => __('Manual'),
                'value' => self::MODE_MANUAL
            ]
        ];
    }
}
