<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Cron\Processor;

use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Cron\AbstractProcessor;
use Mageproxy\Connector\Model\OptimizationManager;
use Mageproxy\Connector\Model\ProviderInterface;

class RevertOptimization extends AbstractProcessor
{
    private OptimizationManager $optimizationManager;

    public function __construct(
        ProviderInterface $provider,
        OptimizationManager $optimizationManager
    ) {
        parent::__construct($provider);
        $this->optimizationManager = $optimizationManager;
    }

    public function process($entity): void
    {
        try {
            $this->optimizationManager->revert($entity, OptimizationInterface::STATUS_FINISHED);
        } catch (\Exception $e) {
            $this->messages[] = $e->getMessage();
        }
    }
}
