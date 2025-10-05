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
use Mageproxy\Connector\Api\OptimizationManagerInterface;
use Mageproxy\Connector\Api\RecordingManagerInterface;
use Mageproxy\Connector\Cron\AbstractProcessor;
use Mageproxy\Connector\Model\ProviderInterface;
use Mageproxy\Connector\Model\Recording\Source\Status;

class UpdateRecording extends AbstractProcessor
{
    private Status $status;
    private RecordingManagerInterface $recordingManager;
    private OptimizationManagerInterface $optimizationManager;

    public function __construct(
        RecordingManagerInterface $recordingManager,
        OptimizationManagerInterface $optimizationManager,
        ProviderInterface $provider,
        Status $status
    ) {
        parent::__construct($provider);
        $this->status = $status;
        $this->recordingManager = $recordingManager;
        $this->optimizationManager = $optimizationManager;
    }

    /**
     * The cron based recording state machine
     *
     * Order matters...
     *
     * Every "non-finished" recording will pass through it every minute
     *
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $entity
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function process($entity): void
    {
        $origStatus = $entity->getStatus();
        if ($this->recordingManager->shouldFinish($entity)) {
            $this->recordingManager->finish($entity); // * -> FINISHED
            if ($this->optimizationManager->deploymentInProgress($entity->getStoreId())) {
                $optimization = $this->optimizationManager->getDeployedOptimization($entity->getStoreId());
                try {
                    $this->optimizationManager->revert($optimization, OptimizationInterface::STATUS_FINISHED);
                } catch (\Exception $e) {}
            }
        } elseif ($this->recordingManager->wasMissed($entity)) {
            $this->recordingManager->missed($entity); // PENDING -> MISSED
        } elseif ($this->recordingManager->shouldStart($entity) || $this->recordingManager->shouldRestart($entity)) {
            $this->recordingManager->start($entity); // PENDING|STOPPED -> RUNNING
        } elseif ($this->recordingManager->shouldStop($entity)) {
            $this->recordingManager->stop($entity); // RUNNING -> STOPPED
        }
        if ($entity->getStatus() !== $origStatus) {
            $this->messages[] = sprintf(
                'Recording %s: %s -> %s',
                $entity->getUuid(),
                $this->status->getLabel($origStatus),
                $this->status->getLabel($entity->getStatus())
            );
        }
    }
}
