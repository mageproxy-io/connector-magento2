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

use Exception;
use Mageproxy\Connector\Cron\AbstractProcessor;
use Mageproxy\Connector\Model\ProviderInterface;

class GetRecordingDeps extends AbstractProcessor
{
    private \Mageproxy\Connector\Model\GetRecordingDeps $getRecordingDeps;

    public function __construct(
        ProviderInterface $provider,
        \Mageproxy\Connector\Model\GetRecordingDeps $getRecordingDeps
    ) {
        parent::__construct($provider);
        $this->getRecordingDeps = $getRecordingDeps;
    }

    /**
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $entity
     * @return void
     */
    public function process($entity): void
    {
        try {
            $this->getRecordingDeps->execute((int) $entity->getId());
        } catch (Exception $e) {
            $this->messages[] = $e->getMessage();
        }
    }
}
