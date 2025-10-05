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

class DeleteRecording implements DeleteRecordingInterface
{
    private Adapter $adapter;

    public function __construct(
        Adapter $adapter
    ) {
        $this->adapter = $adapter;
    }

    public function execute(string $recordingId): void
    {
        try {
            $this->adapter->delete(['id' => $recordingId]);
        } catch (NotFoundException $e) {
            // Recording is already absent remotely; treat as success
            return;
        }
    }
}
