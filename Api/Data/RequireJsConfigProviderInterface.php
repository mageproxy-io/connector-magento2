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

interface RequireJsConfigProviderInterface
{
    const REQUIREJS_MODULE_CONFIG_KEY = 'mageproxy/requirejs-recorder';

    /**
     * @return array Associated array with config key/values
     */
    public function getConfig(): array;
}
