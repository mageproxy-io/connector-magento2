<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Block\Adminhtml\System\Config;

class SignUpComment extends \Magento\Config\Block\System\Config\Form\Field
{
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if (!$element->getValue()) {
            $element->setData(
                'comment',
                'Create a <a href="https://mageproxy.io/plans/free">FREE account</a> on our website.'
            );
        }
        return parent::render($element);

    }

}
