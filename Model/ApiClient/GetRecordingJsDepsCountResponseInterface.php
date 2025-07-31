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

interface GetRecordingJsDepsCountResponseInterface
{
    /**
     * @return int
     */
    public function getCount(): int;

    /**
     * @param int $count
     * @return void
     */
    public function setCount(int $count): void;

    /**
     * @return int
     */
    public function getHdlsCount(): int;

    /**
     * @param int $hdlsCount
     * @return void
     */
    public function setHdlsCount(int $hdlsCount): void;
}
