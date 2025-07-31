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

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Bundle extends AbstractDb
{
    const MAIN_TABLE_NAME = 'mageproxy_optimization_bundle';
    const MAIN_TABLE_ID_FIELD = 'bundle_id';

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, self::MAIN_TABLE_ID_FIELD);
    }

    protected function _beforeSave(AbstractModel $object)
    {
        if ($object->getOptimizationId() === null && $object->getOptimization()) {
            $object->setOptimizationId((int) $object->getOptimization()->getId());
        }
        return $this;
    }
}
