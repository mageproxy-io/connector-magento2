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

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Model\Config;
use Mageproxy\Connector\Model\System\Config\Source\RunMode;

class CreateNewRecordingButton implements ButtonProviderInterface
{
    private UrlInterface $urlBuilder;
    private Config $config;

    public function __construct(
        UrlInterface $urlBuilder,
        Config $config
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Start New Recording'),
            'disabled' => $this->config->getRunMode() !== RunMode::MODE_MANUAL,
            'class' => 'save primary',
            'on_click' => sprintf(
                "location.href = '%s';",
                $this->urlBuilder->getUrl('*/recording/create', ['mode' => RecordingInterface::MODE_IMMEDIATE])
            ),
        ];
    }
}
