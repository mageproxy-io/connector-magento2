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

use Magento\Ui\DataProvider\AbstractDataProvider;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Model\Optimization\Source\Status as StatusOptions;
use Mageproxy\Connector\Model\ResourceModel\Optimization\CollectionFactory;

class OptimizationDataProvider extends AbstractDataProvider
{
    protected array $loadedData;
    private StatusOptions $statusOptions;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        StatusOptions $statusOptions,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->loadedData = [];
        $this->collection = $collectionFactory->create();
        $this->statusOptions = $statusOptions;
    }

    public function getData()
    {
        if ($this->loadedData) {
            return $this->loadedData;
        }

        foreach ($this->collection->getItems() as $item) {
            /** @var  \Mageproxy\Connector\Model\Optimization $item */
            $data = $item->getData();
            $data['status'] = $this->statusOptions->getLabel((int) $data[OptimizationInterface::STATUS]);
            $data['exclude_deps'] = $this->prepareData($item->getExcludeDeps(), 'module_id');
            $data['remove_deps'] = $this->prepareData($item->getRemoveDeps(), 'module_id');
            $data['handles'] = $this->prepareData($item->getHandles(), 'page_handle');
            $this->loadedData[$item->getId()] = $data;
        }

        return $this->loadedData;
    }

    private function prepareData($data, $key): array
    {
        return array_map(function ($item) use ($key) {
            return [
                $key => $item
            ];
        }, $data);
    }

    public function getMeta()
    {
        $meta = parent::getMeta();
        return $meta;
    }
}
