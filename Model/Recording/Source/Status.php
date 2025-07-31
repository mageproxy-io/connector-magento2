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

class Status implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => RecordingInterface::STATUS_PENDING,
                'label' => __('Pending')
            ],
            [
                'value' => RecordingInterface::STATUS_RUNNING,
                'label' => __('Running')
            ],
            [
                'value' => RecordingInterface::STATUS_FINISHED,
                'label' => __('Finished')
            ],
            [
                'value' => RecordingInterface::STATUS_STOPPED,
                'label' => __('Stopped')
            ],
            [
                'value' => RecordingInterface::STATUS_ERROR,
                'label' => __('Error')
            ],
            [
                'value' => RecordingInterface::STATUS_MISSED,
                'label' => __('Missed')
            ],
        ];
    }

    public function getLabel(int $statusCode): ?string
    {
        foreach ($this->toOptionArray() as ['value' => $code, 'label' => $label]) {
            if ($statusCode === (int) $code) {
                return (string) $label;
            }
        }
        return null;
    }
}
