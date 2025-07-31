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

class GetRecordingJsDepsCountResponse implements GetRecordingJsDepsCountResponseInterface
{
    private int $count = 0;
    private int $hdlsCount = 0;

    /**
     * @inheritdoc
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @inheritdoc
     */
    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    /**
     * @inheritdoc
     */
    public function setHdlsCount(int $hdlsCount): void
    {
        $this->hdlsCount = $hdlsCount;
    }

    /**
     * @inheritdoc
     */
    public function getHdlsCount(): int
    {
        return $this->hdlsCount;
    }
}
