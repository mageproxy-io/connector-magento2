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

use Magento\Framework\Exception\NotFoundException;

class DeleteOptimization implements DeleteOptimizationInterface
{
    /**
     * @var \Mageproxy\Connector\Model\ApiClient\Adapter
     */
    private Adapter $adapter;

    public function __construct(
        Adapter $adapter
    ) {
        $this->adapter = $adapter;
    }

    public function execute(string $optimizationId): void
    {
        try {
            $this->adapter->delete(['id' => $optimizationId]);
        } catch (NotFoundException $e) {
            // Optimization is already absent remotely; treat as success
            return;
        }
    }
}
