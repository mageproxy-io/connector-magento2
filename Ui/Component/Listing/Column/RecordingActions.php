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

class RecordingActions extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            $item[$this->getData('name')]['view'] = [
                'href' => $this->context->getUrl(
                    'mageproxy/recording/view',
                    ['id' => $item['recording_id']]
                ),
                'label' => __('View'),
                'hidden' => false,
            ];
        }

        return $dataSource;
    }
}
