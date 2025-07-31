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
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Mageproxy\Connector\Controller\RegistryConstants;

class RecordingGenericButton implements ButtonProviderInterface
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * @var array
     */
    private array $statusIn;

    /**
     * @var \Magento\Framework\Registry
     */
    private Registry $registry;

    /**
     * @var array
     */
    private array $buttonParams;

    /**
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Registry $registry
     * @param array $buttonParams
     * @param array $statusIn
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Registry $registry,
        array $buttonParams = [],
        array $statusIn = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->registry = $registry;
        $this->statusIn = $statusIn;
        $this->buttonParams = $buttonParams;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $recording = $this->registry->registry(RegistryConstants::CURRENT_RECORDING);

        if (!$recording) {
            return [];
        }

        if (!empty($this->statusIn)) {
            if (!in_array($recording->getStatus(), $this->statusIn)) {
                return [];
            }
        }
        return [
            'label' => __($this->buttonParams['label'] ?? 'Button'),
            'on_click' => $this->getOnClickHandler((int) $recording->getId()),
            'class' => $this->buttonParams['class'] ?? '',
            'sort_order' => $this->buttonParams['sort_order'] ?? 10,
        ];
    }

    /**
     * @param int $recordingId
     * @return string
     */
    private function getOnClickHandler(int $recordingId): string
    {
        if ($this->buttonParams['confirm'] ?? false) {
            return sprintf(
                "deleteConfirm('%s', '%s')",
                __('Are you sure you want to do this?'),
                $this->getUrl($recordingId)
            );
        }

        return sprintf(
            "location.href = '%s';",
            $this->getUrl($recordingId)
        );
    }

    /**
     * @param int $recordingId
     * @return string
     */
    private function getUrl(int $recordingId): string
    {
        return $this->urlBuilder->getUrl('*/*/' . $this->buttonParams['action_name'] ?? '', ['id' => $recordingId]);
    }
}
