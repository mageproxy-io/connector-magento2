<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Ui\DataProvider\Listing;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Mageproxy\Connector\Model\ResourceModel\Prefetch as PrefetchResourceModel;
use Psr\Log\LoggerInterface as Logger;

class PrefetchDataProvider extends SearchResult
{
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = \Mageproxy\Connector\Model\Prefetch::TABLE_NAME,
        $resourceModel = PrefetchResourceModel::class,
        $identifierName = \Mageproxy\Connector\Model\Prefetch::TABLE_PRIMARY_KEY
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel,
            $identifierName
        );
    }

    protected function _afterLoadData()
    {
        foreach ($this->_data as $key => $item) {
            $count = 0;
            if (isset($item['rules'])) {
                $rules = $item['rules'];
                if (is_string($rules)) {
                    $decoded = json_decode($rules, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $rules = $decoded;
                    } elseif (preg_match('/^a:\d+:/', $rules)) {
                        $unser = @unserialize($rules, ['allowed_classes' => false]);
                        $rules = is_array($unser) ? $unser : [];
                    } else {
                        $rules = [];
                    }
                }
                if (is_array($rules)) {
                    $count = count($rules);
                }
            }
            $this->_data[$key]['rules_cnt'] = (string)$count;
        }
        return $this;
    }
}
