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

class PageHandlePriority extends \Magento\Ui\Component\Listing\Columns\Column
{
    public static array $pageHandlePriority = [
        'cms_index_index',
        'cms_page_view',
        'catalog_category_view',
        'catalog_product_view',
        'checkout_cart_index',
        'checkout_index_index'
    ];

    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        $fieldName = $this->getData('name');

        foreach ($dataSource['data']['items'] as &$item) {
            $item[$fieldName] = $this->getPriority($item['page_handle']);
        }

        return $dataSource;
    }

    private function getPriority(string $pageHandle): ?int
    {
        $index = array_search($pageHandle, self::$pageHandlePriority, true);
        return $index === false ? null : $index + 1;
    }

}
