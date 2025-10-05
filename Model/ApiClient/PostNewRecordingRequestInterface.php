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

use Magento\Framework\Api\ExtensibleDataInterface;

interface PostNewRecordingRequestInterface extends ExtensibleDataInterface
{
    /**
     * @param string $serviceId
     * @return void
     */
    public function setServiceId(string $serviceId): void;

    /**
     * @return string
     */
    public function getServiceId(): string;

    /**
     * @param string $domain
     * @return void
     */
    public function setDomain(string $domain): void;

    /**
     * @return string
     */
    public function getDomain(): string;

    /**
     * @return void
     */
    public function setStaticVersion(string $staticVersion): void;

    /**
     * @return string
     */
    public function getStaticVersion(): string;
}
