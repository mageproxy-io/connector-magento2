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

class BundleUrl extends \Magento\Ui\Component\Listing\Columns\Column
{
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        $fieldName = $this->getData('name');

        foreach ($dataSource['data']['items'] as &$item) {
            $item[$fieldName] = $this->format($item[$fieldName]);
        }

        return $dataSource;
    }

    private function format(string $url): string
    {
        // Get the latest part of the URL
        $urlParts = explode('/', $url);
        $fileName = end($urlParts);
        return sprintf('<a href="%s" target="_blank">%s</a>', $url, $fileName);
    }
}
