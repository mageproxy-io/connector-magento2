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

class PrefetchActions extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            if (!isset($item['prefetch_id'])) {
                continue;
            }
            $id = (int)$item['prefetch_id'];
            $item[$this->getData('name')] = [
                'edit' => [
                    'href' => $this->context->getUrl('mageproxy/prefetch/edit', ['id' => $id]),
                    'label' => __('Edit'),
                    'hidden' => false,
                ],
                'delete' => [
                    'href' => $this->context->getUrl('mageproxy/prefetch/delete', ['id' => $id]),
                    'label' => __('Delete'),
                    'confirm' => [
                        'message' => __('Are you sure you want to delete these prefetch rules?')
                    ],
                    'hidden' => false,
                ],
            ];
        }

        return $dataSource;
    }
}
