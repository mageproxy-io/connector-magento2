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
use Mageproxy\Connector\Model\ProviderInterface;
use Mageproxy\Connector\Model\SyncOptimization;

class UpdateOptimization extends AbstractProcessor
{
    private SyncOptimization $syncOptimization;

    public function __construct(
        ProviderInterface $provider,
        SyncOptimization $syncOptimization
    ) {
        parent::__construct($provider);
        $this->syncOptimization = $syncOptimization;
    }

    /**
     * @param OptimizationInterface $entity
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function process($entity): void
    {
        $this->syncOptimization->execute($entity);
    }

}
