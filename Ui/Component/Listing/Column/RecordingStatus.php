<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Mageproxy\Connector\Model\Recording\Source\Status;

class RecordingStatus extends Column
{
    private Status $statusSource;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Status $statusSource,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->statusSource = $statusSource;
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = $this->convertStatusCodeToLabel((int) $item[$this->getData('name')]);
            }
        }
        return $dataSource;
    }

    private function convertStatusCodeToLabel(int $statusCode): string
    {
        $label = $this->statusSource->getLabel($statusCode);
        $class = 'grid-recording-status-' . strtolower($label);
        return sprintf('<span class="%s">%s</span>', $class, $label);
    }

}
