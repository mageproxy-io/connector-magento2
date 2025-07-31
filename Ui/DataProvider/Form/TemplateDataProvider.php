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
use Mageproxy\Connector\Model\ResourceModel\Optimization\Template\CollectionFactory;

class TemplateDataProvider extends AbstractDataProvider
{
    protected array $loadedData;

    private StoreFieldRenderer $storeFieldRenderer;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        StoreFieldRenderer $storeFieldRenderer,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->loadedData = [];
        $this->collection = $collectionFactory->create();
        $this->storeFieldRenderer = $storeFieldRenderer;
    }

    public function getData()
    {
        if ($this->loadedData) {
            return $this->loadedData;
        }
        /** @var \Mageproxy\Connector\Model\Optimization\Template $template */
        $template = $this->collection->getFirstItem();
        $data = $template->getData();
        $data['store'] = $this->storeFieldRenderer->getText($template->getStoreId());
        $this->loadedData[$template->getId()] = $data;
        return $this->loadedData;
    }

}
