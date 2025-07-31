<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Cron;

use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Cron\ProcessOptimizations;
use Mageproxy\Connector\Model\ApiClient\GetOptimizationInterface;
use Mageproxy\Connector\Model\ApiClient\GetOptimizationResponseInterface;
use Mageproxy\Connector\Model\ApiClient\OptimizationBundleInterfaceFactory;
use PHPUnit\Framework\TestCase;

class ProcessOptimizationsTest extends TestCase
{
    /**
     * @var \Mageproxy\Connector\Cron\ProcessOptimizations|null
     */
    private $cron;

    private $apiClientMock;

    protected function setUp(): void
    {
        $this->markTestIncomplete('These tests need to move as the cron logic is now using processors');
        $objectManager = Bootstrap::getObjectManager();
        $this->apiClientMock = self::createMock(GetOptimizationInterface::class);
        $this->cron = $objectManager->create(ProcessOptimizations::class, [
            'getOptimizationApiClient' => $this->apiClientMock
        ]);
    }

    public function testCronDoesNotRunWhenModuleDisabled(): void
    {
        $this->apiClientMock->expects(self::never())->method('execute');
        $this->cron->execute();
    }

    public function testCronDoesNotRequestOptimizationsFromAPI(): void
    {
        $this->apiClientMock->expects(self::never())->method('execute');
        $this->cron->execute();
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimizations.php
     */
    public function testCronCallsTheApiOnlyForRequestedOrPublishedOptimizations(): void
    {
        $this->apiClientMock->expects(self::exactly(2))->method('execute');
        $this->cron->execute();
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimizations.php
     */
    public function testCronUpdatesOptimizationsStatus(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $optimizationRepository = $objectManager->create(OptimizationRepositoryInterface::class);

        $publishedApiResponse = $objectManager->create(GetOptimizationResponseInterface::class);
        $publishedApiResponse->setStatus(GetOptimizationResponseInterface::STATUS_PUBLISHED);
        $publishedApiResponse->setBundles([]);

        $readyApiResponse = $objectManager->create(GetOptimizationResponseInterface::class);
        $readyApiResponse->setStatus(GetOptimizationResponseInterface::STATUS_READY);
        $readyApiResponse->setBundles([]);

        $this->apiClientMock->expects(self::exactly(2))
            ->method('execute')
            ->willReturnMap([
                ['requested', $publishedApiResponse],
                ['published', $readyApiResponse],
            ]);

        $this->cron->execute();

        $requested = $optimizationRepository->get('requested');
        $published = $optimizationRepository->get('published');

        self::assertSame(OptimizationInterface::STATUS_PUBLISHED, $requested->getStatus());
        self::assertSame(OptimizationInterface::STATUS_READY, $published->getStatus());
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimizations.php
     */
    public function testCronUpdatesBundles(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $optimizationRepository = $objectManager->create(OptimizationRepositoryInterface::class);
        $optimizationRepository->delete($optimizationRepository->get('requested'));

        $readyApiResponse = $objectManager->create(GetOptimizationResponseInterface::class);
        $readyApiResponse->setStatus(GetOptimizationResponseInterface::STATUS_READY);
        $readyApiResponse->setBundles($this->createBundles());

        $this->apiClientMock->expects(self::once())
            ->method('execute')
            ->with('published')
            ->willReturn($readyApiResponse);

        $this->cron->execute();

        $ready = $optimizationRepository->get('published');
        $bundles = $ready->getBundles();
        self::assertCount(2, $bundles);
    }

    private function createBundles($minified = true): array
    {
        $objectManager = Bootstrap::getObjectManager();
        $bundleFactory = $objectManager->get(OptimizationBundleInterfaceFactory::class);
        $bundle1 = $bundleFactory->create();
        $bundle1->setUrl('https://www.example.com/bundles/core.' . ($minified ? 'min.' : '') . 'js');
        $bundle1->setSriHash('sha384-oqVuAfXRKap7fdgcCY5uykM6+R9GqQ8K/uxy9rx7HNQlGYl1kPzQho1wx4JwY8wC');
        $bundle1->setMinified($minified);
        $bundle2 = $bundleFactory->create();
        $bundle2->setUrl('https://www.example.com/bundles/common.' . ($minified ? 'min.' : '') . 'js');
        $bundle2->setSriHash('sha384-oqVuAfXRKap7fdgcCY5uykM6+R9GqQ8K/uxy9rx7HNQlGYl1kPzfg37wx4JwY8wC');
        $bundle2->setMinified($minified);
        return [$bundle1, $bundle2];
    }
}
