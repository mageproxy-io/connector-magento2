<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\Recording\Source;

use Mageproxy\Connector\Api\Data\RecordingInterface;

class Initiator implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => RecordingInterface::INITIATOR_USER,
                'label' => __('User')
            ],
            [
                'value' => RecordingInterface::INITIATOR_CRON,
                'label' => __('Cron')
            ]
        ];
    }
}
