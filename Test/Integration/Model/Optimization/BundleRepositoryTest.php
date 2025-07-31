<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\Optimization;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\Data\OptimizationBundleInterface;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\OptimizationBundleRepositoryInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use PHPUnit\Framework\TestCase;

class BundleRepositoryTest extends TestCase
{
    /**
     * @var OptimizationBundleRepositoryInterface|null
     */
    private $bundleRepository;

    /**
     * @var SearchCriteriaBuilder|null
     */
    private $searchCriteriaBuilder;

    /**
     * @var string[]
     */
    private $bundles = [
        'bundles/catalog_product_view.js',
        'bundles/cms_index_index.js',
        'bundles/checkout_cart_index.js',
        'bundles/checkout_index_index.js',
        'bundles/cms_view_page.js',
    ];

    protected function setUp(): void
    {
        $this->bundleRepository = Bootstrap::getObjectManager()->create(OptimizationBundleRepositoryInterface::class);
        $this->searchCriteriaBuilder = Bootstrap::getObjectManager()->get(SearchCriteriaBuilder::class);
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_ready.php
     */
    public function testSaveAndGetById(): void
    {
        $bundle = $this->bundleRepository->save($this->createNewBundle());

        $retrievedBundle = $this->bundleRepository->getById((int) $bundle->getId());

        $this->assertEquals($bundle->getId(), $retrievedBundle->getId());
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_ready.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/bundles.php
     */
    public function testGetList(): void
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $result = $this->bundleRepository->getList($searchCriteria);

        $this->assertNotEmpty($result->getItems());
        $this->assertSame(7, $result->getTotalCount());
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_ready.php
     */
    public function testDeleteAndDeleteById(): void
    {
        $bundle = $this->bundleRepository->save($this->createNewBundle());
        $this->bundleRepository->delete($bundle);

        $this->expectException(NoSuchEntityException::class);
        $this->bundleRepository->getById((int) $bundle->getId());

        $bundle = $this->bundleRepository->save($this->createNewBundle());

        $this->bundleRepository->deleteById((int) $bundle->getId());

        $this->expectException(NoSuchEntityException::class);
        $this->bundleRepository->getById($bundle->getId());
    }

    private function createNewBundle(): OptimizationBundleInterface
    {
        $objectManager = Bootstrap::getObjectManager();
        $bundle = $objectManager->create(OptimizationBundleInterface::class);
        $bundleUrl = $this->getRandomBundleUrl();
        $bundle->setUrl($bundleUrl);
        $bundle->setOptimizationId((int) $this->getOptimization('ready')->getId());
        $bundle->setSriHash(hash('sha384', $bundleUrl));
        return $bundle;
    }

    private function getOptimization(string $uuid): OptimizationInterface
    {
        return Bootstrap::getObjectManager()
            ->get(OptimizationRepositoryInterface::class)->get($uuid);
    }

    private function getRandomBundleUrl(): string
    {
        $key = array_rand($this->bundles);
        $bundle = $this->bundles[$key];
        unset($this->bundles[$key]);
        return 'http://example.com/' . $bundle;
    }
}
