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

class AutoRunFieldset extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    protected function _isCollapseState($element)
    {
        return false;
    }
}
