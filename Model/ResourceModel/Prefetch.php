<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Prefetch extends AbstractDb
{
    protected $_serializableFields = [
        'rules' => [[], []]
    ];

    protected function _construct()
    {
        $this->_init(
            \Mageproxy\Connector\Model\Prefetch::TABLE_NAME,
            \Mageproxy\Connector\Model\Prefetch::TABLE_PRIMARY_KEY
        );
    }
}
