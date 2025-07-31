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
use Mageproxy\Connector\Api\Data\OptimizationInterface;

class Status implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            [
                'value' => OptimizationInterface::STATUS_REQUESTED,
                'label' => __('Requested')
            ],
            [
                'value' => OptimizationInterface::STATUS_PUBLISHED,
                'label' => __('Published')
            ],
            [
                'value' => OptimizationInterface::STATUS_READY,
                'label' => __('Ready')
            ],
            [
                'value' => OptimizationInterface::STATUS_FAILED,
                'label' => __('Failed')
            ],
            [
                'value' => OptimizationInterface::STATUS_DEPLOYED,
                'label' => __('Deployed')
            ],
            [
                'value' => OptimizationInterface::STATUS_FINISHED,
                'label' => __('Finished')
            ],
        ];
    }

    public function getLabel($statusCode): ?string
    {
        foreach ($this->toOptionArray() as ['value' => $code, 'label' => $label]) {
            if ($statusCode === (int) $code) {
                return (string) $label;
            }
        }
        return null;
    }
}
