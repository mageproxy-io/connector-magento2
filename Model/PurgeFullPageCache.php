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

use Magento\Framework\App\Cache\TypeListInterface as CacheTypeListInterface;
use Magento\PageCache\Model\Cache\Type;

class PurgeFullPageCache
{
    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    private CacheTypeListInterface $cacheTypeList;

    /**
     * @var bool
     */
    private bool $shouldPurge = false;

    /**
     * @param \Magento\Framework\App\Cache\TypeListInterface $typeList
     */
    public function __construct(
        CacheTypeListInterface $typeList
    ) {
        $this->cacheTypeList = $typeList;
    }

    /**
     * Conditionally executes the cache flush
     *
     * @param bool $force
     * @return void
     */
    public function execute(bool $force = false): void
    {
        if ($this->shouldPurge || $force) {
            $this->cacheTypeList->cleanType(Type::TYPE_IDENTIFIER);
        }
    }

    /**
     * Once enabled, the cache will be purged when execute is invoked
     */
    public function shouldPurge(?bool $flag = null): void
    {
        $this->shouldPurge = $this->shouldPurge || ($flag ?? true);
    }
}
