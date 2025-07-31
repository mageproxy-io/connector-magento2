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

use Mageproxy\Connector\Api\RecordingManagerInterface;
use Mageproxy\Connector\Cron\AbstractProcessor;
use Mageproxy\Connector\Model\ProviderInterface;

class FinishRecording extends AbstractProcessor
{
    private RecordingManagerInterface $recordingManager;

    public function __construct(
        ProviderInterface $provider,
        RecordingManagerInterface $recordingManager
    ) {
        parent::__construct($provider);
        $this->recordingManager = $recordingManager;
    }

    public function process($entity): void
    {
        try {
            $this->recordingManager->finish($entity);
        } catch (\Exception $e) {
            $this->messages[] = $e->getMessage();
        }
    }
}
