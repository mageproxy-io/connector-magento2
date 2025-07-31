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
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Model\ResourceModel\Recording as RecordingResourceModel;
use Psr\Log\LoggerInterface as Logger;

class RecordingDataProvider extends SearchResult
{

    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = RecordingResourceModel::TABLE_NAME,
        $resourceModel = RecordingResourceModel::class,
        $identifierName = RecordingResourceModel::TABLE_PRIMARY_KEY
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
        $select = $this->getConnection()->select();
        $ids = array_map(function ($item) {
            return $item['recording_id'];
        }, $this->_data);
        $select->from($this->getTable('mageproxy_optimization'), ['recording_id'])
            ->where('recording_id in (?)', $ids)
            ->where('status = ?', OptimizationInterface::STATUS_DEPLOYED);
        $result = $this->getConnection()->fetchCol($select);
        foreach($this->_data as $key => $item) {
            $this->_data[$key]['deployed'] = in_array($item['recording_id'], $result) ? '1' : '0';
        }
        return $this;
    }

}
