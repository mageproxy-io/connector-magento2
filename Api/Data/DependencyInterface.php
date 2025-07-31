<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Api\Data;

interface DependencyInterface
{
    const RECORDING_ID = 'recording_id';
    const PAGE_HANDLE = 'page_handle';
    const MODULE_ID = 'module_id';

    /**
     * @return string|null
     */
    public function getModuleId(): ?string;

    /**
     * @param string $path
     * @return void
     */
    public function setModuleId(string $path): void;

    /**
     * @return int|null
     */
    public function getRecordingId(): ?int;

    /**
     * @param int $recordingId
     * @return void
     */
    public function setRecordingId(int $recordingId): void;

    /**
     * @return string|null
     */
    public function getPageHandle(): ?string;

    /**
     * @param string $pageHandle
     * @return mixed
     */
    public function setPageHandle(string $pageHandle);
}
