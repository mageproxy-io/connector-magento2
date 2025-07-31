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
use Mageproxy\Connector\Model\RouteToPrefetchBundleResolver;

class Prefetch implements ArgumentInterface
{
    private const PREFETCH_ON_VIEWPORT = 'viewport';
    private const PREFETCH_ON_INTERACTION = 'interaction';

    /**
     * @var \Mageproxy\Connector\Model\RouteToPrefetchBundleResolver
     */
    private RouteToPrefetchBundleResolver $routeToPrefetchBundleResolver;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private Json $json;
    private \Mageproxy\Connector\Model\Config $config;

    /**
     * @param \Mageproxy\Connector\Model\RouteToPrefetchBundleResolver $routeToPrefetchBundleResolver
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     */
    public function __construct(
        RouteToPrefetchBundleResolver $routeToPrefetchBundleResolver,
        Json $json,
        \Mageproxy\Connector\Model\Config $config
    ) {
        $this->routeToPrefetchBundleResolver = $routeToPrefetchBundleResolver;
        $this->json = $json;
        $this->config = $config;
    }

    public function getRules(): array
    {
        $prefetchRulesFromConfig = $this->config->getPrefetchRules();
        // $prefetchRulesFromConfig = [
        //     [
        //         'selector' => 'a[href*="/customer/account/create"]',
        //         'bundle' => 'customer/account/create',
        //     ],
        //     [
        //         'selector' => 'a[class*="viewcart"]',
        //         'bundle' => 'checkout/cart/index',
        //     ],
        //     [
        //         'selector' => 'a[href*="/contact"]',
        //         'bundle' => 'contact/index/index',
        //     ],
        //     [
        //         'selector' => 'div[class=product-item-info]',
        //         'bundle' => 'catalog/product/view',
        //         'prefetch_on' => self::PREFETCH_ON_VIEWPORT
        //     ],
        //     [
        //         'selector' => 'li[class*=category-item]',
        //         'bundle' => 'catalog/category/view'
        //     ],
        //     [
        //         'selector' => 'button[id=top-cart-btn-checkout]',
        //         'bundle' => 'checkout/index/index'
        //     ]
        // ];

        $rules = [];
        foreach ($prefetchRulesFromConfig as $rule) {
            try {
                $bundles = $this->routeToPrefetchBundleResolver->resolve($rule['bundle']);
            } catch (NoSuchEntityException $e) {
                continue;
            }
            if (empty($bundles)) {
                continue;
            }
            $rules[] = [
                'selector' => $rule['selector'],
                'bundles' => $bundles,
                'prefetch_on' =>  $rule['prefetch_on'] ?? self::PREFETCH_ON_INTERACTION
            ];
        }
        return $rules;
    }

    public function getPrefetchJson(): string
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
}
