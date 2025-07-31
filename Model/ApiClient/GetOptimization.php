<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\ApiClient;

class GetOptimization implements GetOptimizationInterface
{
    private Adapter $adapter;

    public function __construct(
        Adapter $adapter
    ) {
        $this->adapter = $adapter;
    }

    public function execute(string $optimizationId): ?GetOptimizationResponseInterface
    {
        return $this->adapter->get(['id' => $optimizationId]);
    }
}
