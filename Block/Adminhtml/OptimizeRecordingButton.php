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

use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Mageproxy\Connector\Controller\RegistryConstants;

class OptimizeRecordingButton implements ButtonProviderInterface
{
    private \Magento\Framework\UrlInterface $urlBuilder;
    private Registry $registry;

    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        Registry $registry
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->registry = $registry;
    }

    public function getButtonData()
    {
        $recordingId = $this->registry->registry(RegistryConstants::CURRENT_RECORDING);
        return [
            'label' => __('Optimize'),
            'on_click' => sprintf("location.href = '%s';", $this->urlBuilder->getUrl('*/recording/optimize', ['recording_id' => $recordingId])),
            'class' => 'action-primary',
            'sort_order' => 100,
        ];
    }
}
