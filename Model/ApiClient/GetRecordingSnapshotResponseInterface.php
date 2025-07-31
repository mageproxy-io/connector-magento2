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

interface GetRecordingSnapshotResponseInterface
{
    /**
     * Get the recording UUID.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * @param string $id UUID
     * @return void
     */
    public function setId(string $id): void;

    /**
     * Get the recording checksum
     *
     * This will change if the recording is modified in any way, including:
     * - number of dependencies
     * - number of handles
     * - distribution of dependencies
     *
     * @return string
     */
    public function getChecksum(): string;

    /**
     * @param string $checkSum
     * @return void
     */
    public function setChecksum(string $checkSum): void;

    /**
     * @return int
     */
    public function getHdlsCnt(): int;

    /**
     * @param int $hdlsCnt
     * @return void
     */
    public function setHdlsCnt(int $hdlsCnt): void;

    /**
     * @return int
     */
    public function getDepsCnt(): int;

    /**
     * @param int $depsCnt
     * @return void
     */
    public function setDepsCnt(int $depsCnt): void;
}
