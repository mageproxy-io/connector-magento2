<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\RequireJs\ConfigProvider;

use Mageproxy\Connector\Api\Data\RequireJsConfigProviderInterface;
use Mageproxy\Connector\Api\OptimizationManagerInterface;
use Mageproxy\Connector\Helper\Url;

class OptimizationDeployed implements RequireJsConfigProviderInterface
{
    /**
     * @var \Mageproxy\Connector\Api\OptimizationManagerInterface
     */
    private OptimizationManagerInterface $optimizationManager;

    /**
     * @var \Mageproxy\Connector\Helper\Url
     */
    private Url $urlHelper;

    /**
     * @param \Mageproxy\Connector\Api\OptimizationManagerInterface $optimizationManager
     * @param \Mageproxy\Connector\Helper\Url $urlHelper
     */
    public function __construct(
        OptimizationManagerInterface $optimizationManager,
        Url $urlHelper
    ) {
        $this->optimizationManager = $optimizationManager;
        $this->urlHelper = $urlHelper;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        if (!$this->optimizationManager->deploymentInProgress()) {
            return [];
        }

        /** @var \Mageproxy\Connector\Model\Optimization $optimization */
        $optimization = $this->optimizationManager->getDeployedOptimization();
        return [
            'baseUrl' => $this->urlHelper->getStaticBaseUrl($optimization),
            'config' => [
                self::REQUIREJS_MODULE_CONFIG_KEY => [
                    'origBaseUrl' => $this->urlHelper->getDefaultStaticBaseUrl(),
                    'optimized' => true,
                    'minified' => $optimization->getMinifyJs()
                ]
            ]
        ];
    }
}
