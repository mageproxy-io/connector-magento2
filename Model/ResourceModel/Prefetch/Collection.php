<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\ResourceModel\Prefetch;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mageproxy\Connector\Model\Prefetch as PrefetchModel;
use Mageproxy\Connector\Model\ResourceModel\Prefetch as PrefetchResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(PrefetchModel::class, PrefetchResource::class);
    }

    protected function _afterLoad(): self
    {
        /** @var \Mageproxy\Connector\Model\Prefetch $item */
        foreach ($this->_items as $item) {
            $this->getResource()->unserializeFields($item);
        }

        return parent::_afterLoad();
    }
}
