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

class PrefetchRulesButton implements ButtonProviderInterface
{
    private UrlInterface $urlBuilder;

    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    public function getButtonData()
    {
        return [
            'label' => __('Prefetch Rules'),
            'class' => 'secondary',
            'on_click' => sprintf(
                "location.href = '%s';",
                $this->urlBuilder->getUrl('mageproxy/prefetch/index')
            ),
            'sort_order' => 5
        ];
    }
}
