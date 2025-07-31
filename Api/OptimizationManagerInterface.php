<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Api;

use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\Data\OptimizationTemplateInterface;
use Mageproxy\Connector\Api\Data\RecordingInterface;

interface OptimizationManagerInterface
{
    /**
     * Deploy an optimization, moves status to "DEPLOYED"
     *
     * Enforces only one optimization to be deployed at a time for a given store
     *
     * @param \Mageproxy\Connector\Api\Data\OptimizationInterface $optimization
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deploy(OptimizationInterface $optimization): void;

    /**
     * Revert a deployed optimization, moves status back to "READY"
     *
     * @param \Mageproxy\Connector\Api\Data\OptimizationInterface $optimization
     * @param int $afterStatus
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function revert(OptimizationInterface $optimization, int $afterStatus = OptimizationInterface::STATUS_READY): void;

    /**
     * Determine whether an optimization has been deployed for the current or given store
     *
     * @param int|null $storeId
     * @return bool
     */
    public function deploymentInProgress(?int $storeId = null): bool;

    /**
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $recording
     * @param string $requestedBy
     * @param \Mageproxy\Connector\Api\Data\OptimizationTemplateInterface $template
     * @return \Mageproxy\Connector\Api\Data\OptimizationInterface
     */
    public function request(RecordingInterface $recording, string $requestedBy, OptimizationTemplateInterface $template): OptimizationInterface;

    /**
     * Get the currently deployed optimization instance (if any) for the current or given store
     *
     * @param int|null $storeId
     * @return \Mageproxy\Connector\Api\Data\OptimizationInterface|null
     */
    public function getDeployedOptimization(?int $storeId = null): ?OptimizationInterface;
}
