<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\ViewModel;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageproxy\Connector\Api\Data\PrefetchInterface;
use Mageproxy\Connector\Model\Config;
use Mageproxy\Connector\Model\PrefetchFactory;
use Mageproxy\Connector\Model\ResourceModel\Prefetch as PrefetchResource;
use Mageproxy\Connector\Model\RouteToPrefetchBundleResolver;

class Prefetch implements ArgumentInterface
{
    /**
     * @var \Mageproxy\Connector\Model\RouteToPrefetchBundleResolver
     */
    private RouteToPrefetchBundleResolver $routeToPrefetchBundleResolver;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private Json $json;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var \Mageproxy\Connector\Model\PrefetchFactory
     */
    private PrefetchFactory $prefetchFactory;

    /**
     * @var \Mageproxy\Connector\Model\ResourceModel\Prefetch
     */
    private PrefetchResource $prefetchResource;

    /**
     * @var \Mageproxy\Connector\Model\Config
     */
    private Config $config;

    /**
     * @param \Mageproxy\Connector\Model\RouteToPrefetchBundleResolver $routeToPrefetchBundleResolver
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Mageproxy\Connector\Model\PrefetchFactory $prefetchFactory
     * @param \Mageproxy\Connector\Model\ResourceModel\Prefetch $prefetchResource
     * @param \Mageproxy\Connector\Model\Config $config
     */
    public function __construct(
        RouteToPrefetchBundleResolver $routeToPrefetchBundleResolver,
        Json $json,
        StoreManagerInterface $storeManager,
        PrefetchFactory $prefetchFactory,
        PrefetchResource $prefetchResource,
        Config $config
    ) {
        $this->routeToPrefetchBundleResolver = $routeToPrefetchBundleResolver;
        $this->json = $json;
        $this->storeManager = $storeManager;
        $this->prefetchFactory = $prefetchFactory;
        $this->prefetchResource = $prefetchResource;
        $this->config = $config;
    }

    public function getRules(): array
    {
        $storeId = (int) $this->storeManager->getStore()->getId();
        if (!$this->config->getPrefetchEnabled($storeId)) {
            return [];
        }
        $rules = [];
        foreach ($this->loadRulesForStore($storeId) as $rule) {
            try {
                $bundles = $this->routeToPrefetchBundleResolver->resolve($rule['bundle_pattern']);
            } catch (NoSuchEntityException $e) {
                continue;
            }
            if (empty($bundles)) {
                continue;
            }
            $rules[] = [
                'selector' => $rule['selector'],
                'bundles' => $bundles,
                'prefetch_on' =>  $rule['prefetch_on'] ?? PrefetchInterface::TRIGGER_INTERACTION
            ];
        }
        return $rules;
    }

    public function getPrefetchRulesJson(): string
    {
        $prefetchRules = array_reduce($this->getRules(), function ($acc, $rule) {
            $acc[$rule['selector']] = [
                'bundles' => $rule['bundles'],
                'prefetchOn' => $rule['prefetch_on']
            ];
            return $acc;
        }, []);
        return $this->json->serialize($prefetchRules);
    }

    private function loadRulesForStore(int $storeId): array
    {
        $prefetchModel = $this->prefetchFactory->create();
        $this->prefetchResource->load($prefetchModel, $storeId, 'store_id');
        if (!$prefetchModel->getId()) {
            // Fallback to default store (0)
            $this->prefetchResource->load($prefetchModel, 0, 'store_id');
            if (!$prefetchModel->getId()) {
                return [];
            }
        }
        $rules = $prefetchModel->getData('rules');
        return is_array($rules) ? $rules : [];
    }
}
