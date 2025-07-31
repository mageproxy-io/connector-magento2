<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\System\Config\Backend;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Data\Form\Element\Factory;

class AutoRunSchedule extends AbstractFieldArray
{
    private $elementFactory;

    public function __construct(
        Context $context,
        Factory $elementFactory,
        array   $data = []
    ) {
        $this->elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->addColumn('record_for', ['label' => __('Record for')]);
        $this->addColumn('record_time_unit', ['label' => __('Duration')]);
        $this->addColumn('pause_for', ['label' => __('Pause for')]);
        $this->addColumn('pause_time_unit', ['label' => __('Duration')]);
        $this->_addAfter = false;

        parent::_construct();
    }

    public function renderCellTemplate($columnName)
    {
        if (isset($this->_columns[$columnName])) {
            switch ($columnName) {
                case 'record_for':
                    // Generate value/label pairs from 5 till 30 minutes
                    for ($i = 5; $i <= 30; $i += 5) {
                        $options[] = ['value' => $i, 'label' => $i];
                    }
                    $element = $this->elementFactory->create('select');
                    $element->setForm(
                        $this->getForm()
                    )->setName(
                        $this->_getCellInputElementName($columnName)
                    )->setHtmlId(
                        $this->_getCellInputElementId('<%- _id %>', $columnName)
                    )->setValues(
                        $options
                    );
                    return str_replace("\n", '', $element->getElementHtml());
                case 'record_time_unit':
                    $options = [
                        ['value' => 'm', 'label' => 'Minutes'],
                    ];
                    $element = $this->elementFactory->create('select');
                    $element->setForm(
                        $this->getForm()
                    )->setName(
                        $this->_getCellInputElementName($columnName)
                    )->setHtmlId(
                        $this->_getCellInputElementId('<%- _id %>', $columnName)
                    )->setValues(
                        $options
                    );
                    return str_replace("\n", '', $element->getElementHtml());
                case 'pause_for':
                    for ($i = 0; $i <= 10; $i++) {
                        $options[] = ['value' => $i, 'label' => $i];
                    }
                    for ($i = 15; $i <= 30; $i += 5) {
                        $options[] = ['value' => $i, 'label' => $i];
                    }
                    $element = $this->elementFactory->create('select');
                    $element->setForm(
                        $this->getForm()
                    )->setName(
                        $this->_getCellInputElementName($columnName)
                    )->setHtmlId(
                        $this->_getCellInputElementId('<%- _id %>', $columnName)
                    )->setValues(
                        $options
                    );
                    return str_replace("\n", '', $element->getElementHtml());
                case 'pause_time_unit':
                    $options = [
                        ['value' => 'm', 'label' => 'Minutes'],
                        ['value' => 'h', 'label' => 'Hours'],
                        ['value' => 'd', 'label' => 'Days']
                    ];
                    $element = $this->elementFactory->create('select');
                    $element->setForm(
                        $this->getForm()
                    )->setName(
                        $this->_getCellInputElementName($columnName)
                    )->setHtmlId(
                        $this->_getCellInputElementId('<%- _id %>', $columnName)
                    )->setValues(
                        $options
                    );
                    return str_replace("\n", '', $element->getElementHtml());
            }
        }

        return parent::renderCellTemplate($columnName);
    }
}
