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

interface DeleteOptimizationInterface
{
    /**
     * @param string $optimizationId
     * @return void
     */
    public function execute(string $optimizationId): void;
}
