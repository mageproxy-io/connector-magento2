<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\ApiClient\Auth;

use Mageproxy\Connector\Model\Config;

class ApiKey implements AuthStrategyInterface
{
    const API_KEY_HEADER = 'X-Api-Key';
    const SERVICE_ID_HEADER = 'X-Service-Id';

    private Config $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    public function addHeader(array &$headers): void
    {
        $headers[self::API_KEY_HEADER] = $this->config->getApiKey();
        $headers[self::SERVICE_ID_HEADER] = $this->config->getServiceId();
    }
}
