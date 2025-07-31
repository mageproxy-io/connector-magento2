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

use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class BackButton implements ButtonProviderInterface
{
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    private RedirectInterface $redirect;

    /**
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     */
    public function __construct(
        RedirectInterface $redirect
    ) {
        $this->redirect = $redirect;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->redirect->getRefererUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }
}
