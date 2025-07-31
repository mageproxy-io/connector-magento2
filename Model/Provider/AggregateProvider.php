<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\Provider;

use Mageproxy\Connector\Model\ProviderInterface;

class AggregateProvider implements ProviderInterface
{
    /**
     * @var \Mageproxy\Connector\Model\ProviderInterface[]
     */
    private array $providers;

    public function __construct(
        array $providers = []
    ) {
        $this->providers = $providers;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        $items = [];
        foreach ($this->providers as $provider) {
            $items = array_merge($items, $provider->getItems());
        }
        return $items;
    }
}
