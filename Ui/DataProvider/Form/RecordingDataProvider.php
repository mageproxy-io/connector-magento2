<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Ui\DataProvider\Form;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Model\Recording\Source\Status;
use Mageproxy\Connector\Model\ResourceModel\Recording\CollectionFactory;

class RecordingDataProvider extends AbstractDataProvider
{
    protected array $loadedData;

    protected array $statusOptions;
    private \Magento\Framework\App\RequestInterface $request;
    private StoreManagerInterface $storeManager;
    private Status $status;
    private StoreFieldRenderer $storeFieldRenderer;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\App\RequestInterface $request,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        Status $status,
        StoreFieldRenderer $storeFieldRenderer,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->loadedData = [];
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->status = $status;
        $this->storeFieldRenderer = $storeFieldRenderer;
    }

    public function getData()
    {
        if ($this->loadedData) {
            return $this->loadedData;
        }

        foreach ($this->collection->getItems() as $item) {
            $data = $item->getData();
            $data = $this->mapStatusLabel($data);
            $data['store'] = $this->storeFieldRenderer->getText((int)$data['store_id']);
            $this->loadedData[$item->getId()] = $data;
        }

        return $this->loadedData;
    }

    private function mapStoreLabel(array $data): array
    {
        $store = $this->storeManager->getStore($data['store_id']);
        $storeName = $store->getName();
        $storeGroupName = $this->storeManager->getGroup($store->getStoreGroupId())->getName();
        $websiteName = $this->storeManager->getWebsite($store->getWebsiteId())->getName();
        $storeName = $websiteName . ' > ' . $storeGroupName . ' > ' . $storeName;
        $data['store'] = $storeName;
        return $data;
    }

    private function mapStatusLabel(array $data): array
    {
        $data[RecordingInterface::STATUS] = $this->status->getLabel((int) $data[RecordingInterface::STATUS]);
        return $data;
    }

    public function getMeta()
    {
        $this->getRequestFieldName();
        $meta = parent::getMeta();
        if ($this->request->getParam('mode') === RecordingInterface::MODE_IMMEDIATE) {
            $meta['recording'] = [
                'children' => [
                    'scheduled_at' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'visible' => false
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }
        return $meta;
    }
}
