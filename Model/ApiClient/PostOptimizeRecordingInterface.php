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

interface PostOptimizeRecordingInterface
{
    /**
     * @param string $recordingId
     * @param \Mageproxy\Connector\Model\ApiClient\PostOptimizeRecordingRequestInterface $request
     * @return \Mageproxy\Connector\Model\ApiClient\PostOptimizeRecordingResponseInterface|null
     */
    public function execute(string $recordingId, PostOptimizeRecordingRequestInterface $request): ?PostOptimizeRecordingResponseInterface;
}
