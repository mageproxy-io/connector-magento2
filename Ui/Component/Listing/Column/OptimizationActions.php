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

use Magento\Ui\Component\Listing\Columns\Column;

class OptimizationActions extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = [
                    'view' => [
                        'callback' => [
                            [
                                'provider' => 'mageproxy_recording_form.mageproxy_recording_form.optimizations.view_optimization_modal.view_optimization_insert_form',
                                'target' => 'destroyInserted',
                            ],
                            [
                                'provider' => 'mageproxy_recording_form.mageproxy_recording_form.optimizations.view_optimization_modal',
                                'target' => 'openModal'
                            ],
                            [
                                'provider' => 'mageproxy_recording_form.mageproxy_recording_form.optimizations.view_optimization_modal.view_optimization_insert_form',
                                'target' => 'render',
                                'params' => [
                                    'optimization_id' => $item['optimization_id'],
                                ],
                            ]
                        ],
                        'href' => '#',
                        'label' => __('View'),
                        'hidden' => false,
                    ],
                ];
            }
        }
        return $dataSource;
    }
}
