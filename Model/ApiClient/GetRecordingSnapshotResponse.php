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

class GetRecordingSnapshotResponse implements GetRecordingSnapshotResponseInterface
{
    /**
     * @var string
     */
    private string $id;

    /**
     * @var string
     */
    private string $checksum;

    /**
     * @var int
     */
    private int $hdlsCnt;

    /**
     * @var int
     */
    private int $depsCnt;

    /**
     * @inheritdoc
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @inheritdoc
     */
    public function getChecksum(): string
    {
        return $this->checksum;
    }

    /**
     * @inheritdoc
     */
    public function setChecksum(string $checkSum): void
    {
        $this->checksum = $checkSum;
    }

    /**
     * @inheritdoc
     */
    public function getHdlsCnt(): int
    {
        return $this->hdlsCnt;
    }

    /**
     * @inheritdoc
     */
    public function setHdlsCnt(int $hdlsCnt): void
    {
        $this->hdlsCnt = $hdlsCnt;
    }

    /**
     * @inheritdoc
     */
    public function getDepsCnt(): int
    {
        return $this->depsCnt;
    }

    /**
     * @inheritdoc
     */
    public function setDepsCnt(int $depsCnt): void
    {
        $this->depsCnt = $depsCnt;
    }
}
