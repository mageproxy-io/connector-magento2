<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\ResourceModel\Optimization;

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mageproxy\Connector\Model\Optimization as Model;
use Mageproxy\Connector\Model\ResourceModel\Optimization as ResourceModel;

class Collection extends AbstractCollection
{
    private \Mageproxy\Connector\Model\ResourceModel\Recording\CollectionFactory $collectionFactory;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        \Mageproxy\Connector\Model\ResourceModel\Recording\CollectionFactory $collectionFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->collectionFactory = $collectionFactory;
    }

    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }

    protected function _afterLoad(): self
    {
        $recordingIds = [];
        /** @var \Mageproxy\Connector\Model\Optimization $item */
        foreach ($this as $item) {
            $recordingIds[] = (int) $item->getRecordingId();
        }

        $recordingCollection = $this->collectionFactory->create();
        $recordingCollection->addFieldToFilter('recording_id', [
            'in' => array_unique($recordingIds)
        ]);

        foreach ($this as $item) {
            /** @var \Mageproxy\Connector\Model\Recording $recording */
            $recording = $recordingCollection->getItemById($item->getRecordingId());
            $item->setRecording($recording);
        }

        foreach ($this->_items as $item) {
            $this->getResource()->unserializeFields($item);
        }

        return parent::_afterLoad();
    }
}
