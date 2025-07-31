<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\RequireJs;

use Mageproxy\Connector\Api\Data\RequireJsConfigProviderInterface;

class CompositeConfigProvider implements RequireJsConfigProviderInterface
{
    private array $configProviders;

    public function __construct(
        array $configProviders = []
    ) {
        $this->configProviders = $configProviders;
    }

    public function getConfig(): array
    {
        $mergedConfig = [];
        foreach ($this->configProviders as $configProvider) {
            $providerConfig = $configProvider->getConfig();
            $mergedConfig = array_replace_recursive($mergedConfig, $providerConfig);
        }
        return $mergedConfig;
    }
}
