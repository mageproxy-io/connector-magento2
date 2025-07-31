<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */

declare(strict_types=1);

namespace Mageproxy\Connector\Block\Adminhtml;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class TimelineButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        return [
            'label' => __('Timeline'),
            'data_attribute' => [
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => 'mageproxy_recording_form.mageproxy_recording_form.deps_chart_modal',
                                'actionName' => 'openModal'
                            ],
                        ],
                    ],
                ]
            ],
            'on_click' => '',
            'sort_order' => 30
        ];
    }
}
