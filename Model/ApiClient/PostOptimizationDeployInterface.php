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

interface PostOptimizationDeployInterface
{
    /**
     * @param string $optimizationId uuid of the optimization
     * @return void
     */
    public function execute(string $optimizationId): void;
}
