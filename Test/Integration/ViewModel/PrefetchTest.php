<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\ViewModel;

use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Model\RouteToPrefetchBundleResolver;
use Mageproxy\Connector\ViewModel\Prefetch;
use PHPUnit\Framework\TestCase;

class PrefetchTest extends TestCase
{
    public function testFoo(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        $resolverMock = $this->createMock(RouteToPrefetchBundleResolver::class);
        $resolverMock->method('resolve')
            ->willReturnCallback(
                function ($route) {
                    $route = str_replace('/', '.', $route);
                    return [
                        "https://static.example.com/bundles/$route.0.min.js"
                    ];
                }
            );

        $viewModel = $objectManager->create(Prefetch::class, [
            'routeToPrefetchBundleResolver' => $resolverMock
        ]);

        $result = $viewModel->getPrefetchJson();
        $this->assertJson($result);
    }
}
