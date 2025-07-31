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

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class OptimizationTemplateDeleteButton implements ButtonProviderInterface
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private RequestInterface $request;

    /**
     * @param \Magento\Backend\Model\UrlInterface $urlBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        UrlInterface $urlBuilder,
        RequestInterface $request
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        if (!$this->getTemplateId()) {
            return [];
        }
        return [
            'label' => __('Delete'),
            'class' => 'primary',
            'on_click' => sprintf(
                "deleteConfirm('%s', '%s')",
                __('Are you sure you want to do this?'),
                $this->getDeleteUrl()
            ),
            'sort_order' => 20,
        ];
    }

    /**
     * @return string
     */
    private function getDeleteUrl(): string
    {
        return $this->urlBuilder->getUrl('*/*/delete', ['id' => $this->getTemplateId()]);
    }

    /**
     * @return int
     */
    private function getTemplateId(): int
    {
        return (int) $this->request->getParam('id');
    }
}
