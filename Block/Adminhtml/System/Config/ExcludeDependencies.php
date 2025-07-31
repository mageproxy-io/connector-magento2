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

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class ExcludeDependencies extends AbstractFieldArray
{
    protected function _construct()
    {
        $this->addColumn('dependency', ['label' => __('Dependency')]);
        $this->_addAfter = false;
        parent::_construct();
    }
}
