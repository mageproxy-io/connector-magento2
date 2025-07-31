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

interface GetRecordingSnapshotInterface
{
    /**
     * Get recording snapshot from the Mageproxy API
     *
     * @param string $uuid
     * @return \Mageproxy\Connector\Model\ApiClient\GetRecordingSnapshotResponseInterface|null
     * @throws \Mageproxy\Connector\Model\ApiClient\Exception\ApiException
     * @throws \Magento\Framework\Exception\AuthenticationException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute(string $uuid): ?GetRecordingSnapshotResponseInterface;
}
