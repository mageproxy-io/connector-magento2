<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model;

use Magento\Store\Model\StoreManagerInterface;
use Mageproxy\Connector\Api\OptimizationManagerInterface;

class RouteToPrefetchBundleResolver
{
    /**
     * @var \Mageproxy\Connector\Api\OptimizationManagerInterface
     */
    private OptimizationManagerInterface $optimizationManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param \Mageproxy\Connector\Api\OptimizationManagerInterface $optimizationManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        OptimizationManagerInterface $optimizationManager,
        StoreManagerInterface $storeManager
    ) {
        $this->optimizationManager = $optimizationManager;
        $this->storeManager = $storeManager;
    }

    /**
     * @param string $route
     * @return array|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function resolve(string $route): ?array
    {
        if (!$this->optimizationManager->deploymentInProgress()) {
            return [];
        }

        $optimization = $this->optimizationManager->getDeployedOptimization(
            (int) $this->storeManager->getStore()->getId()
        );

        $bundles = $optimization->getBundles();

        $route = str_replace(['/', '_'], '.', $route);

        return array_reduce($bundles, function ($acc, $bundle) use ($route) {
            if (strpos($bundle->getUrl(), $route) !== false) {
                $acc[] = $bundle->getUrl();
            }
            return $acc;
        }, []);
    }
}
