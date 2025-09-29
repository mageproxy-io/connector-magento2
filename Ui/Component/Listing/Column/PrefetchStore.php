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

use Magento\Ui\Component\Listing\Columns\Column;
use Mageproxy\Connector\Ui\DataProvider\Form\StoreFieldRenderer;

class PrefetchStore extends Column
{
    private StoreFieldRenderer $storeFieldRenderer;

    public function __construct(
        StoreFieldRenderer $storeFieldRenderer,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->storeFieldRenderer = $storeFieldRenderer;
    }

    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);
        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }
        foreach ($dataSource['data']['items'] as &$item) {
            $storeId = isset($item['store_id']) ? (int)$item['store_id'] : 0;
            $item['store_id'] = $this->storeFieldRenderer->getText($storeId);
        }
        return $dataSource;
    }
}
