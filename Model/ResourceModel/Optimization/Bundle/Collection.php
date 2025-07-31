<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\ResourceModel\Optimization\Bundle;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mageproxy\Connector\Model\Optimization\Bundle as BundleModel;
use Mageproxy\Connector\Model\ResourceModel\Optimization\Bundle as BundleResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(BundleModel::class, BundleResourceModel::class);
    }
}
