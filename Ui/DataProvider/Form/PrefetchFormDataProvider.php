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

use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Mageproxy\Connector\Model\ResourceModel\Prefetch\CollectionFactory;

class PrefetchFormDataProvider extends AbstractDataProvider
{
    protected array $loadedData = [];
    private RequestInterface $request;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->request = $request;
    }

    public function getData()
    {
        if ($this->loadedData) {
            return $this->loadedData;
        }
        $id = (int)$this->request->getParam('id');
        if ($id) {
            $item = $this->collection->addFieldToFilter('prefetch_id', $id)->getFirstItem();
            if ($item && $item->getId()) {
                $rules = $item->getRules();
                // Add a record_id field to bind to the dynamic rows component
                foreach ($rules as $index => $rule) {
                    $rules[$index]['record_id'] = (string) $index;
                }
                $data['rules'] = $rules;
                $data['prefetch_id'] = $item->getId();
                $data['store_id'] = $item->getStoreId();
                $this->loadedData[$item->getId()] = $data;
            }
        }
        return $this->loadedData;
    }
}
