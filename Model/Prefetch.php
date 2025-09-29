<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model;

use Magento\Framework\Model\AbstractModel;
use Mageproxy\Connector\Api\Data\PrefetchInterface;

class Prefetch extends AbstractModel implements PrefetchInterface
{
    public const TABLE_NAME = 'mageproxy_prefetch';
    public const TABLE_PRIMARY_KEY = 'prefetch_id';

    protected function _construct()
    {
        $this->_init(ResourceModel\Prefetch::class);
    }

    public function getId()
    {
        return $this->getData(self::TABLE_PRIMARY_KEY);
    }

    public function getStoreId(): int
    {
        return (int)$this->getData(self::STORE_ID);
    }

    public function setStoreId(int $storeId): void
    {
        $this->setData(self::STORE_ID, $storeId);
    }

    public function getRules(): array
    {
        $rules = $this->getData(self::RULES);
        return is_array($rules) ? $rules : [];
    }

    public function setRules(array $rules): void
    {
        $this->setData(self::RULES, $rules);
    }
}
