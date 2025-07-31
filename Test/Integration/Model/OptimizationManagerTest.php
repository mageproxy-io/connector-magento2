<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Store\StoreManager;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\OptimizationManagerInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Api\OptimizationTemplateRepositoryInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Model\ApiClient\PostOptimizeRecordingInterface;
use Mageproxy\Connector\Model\ApiClient\PostOptimizeRecordingResponseInterface;
use Mageproxy\Connector\Model\OptimizationManager;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Mageproxy\Connector\Model\OptimizationManager
 */
class OptimizationManagerTest extends TestCase
{
    private ?OptimizationManager $optimizationManager = null;
    private ?StoreManager $storeManager = null;

    protected function setUp(): void
    {
        parent::setUp();
        $objectManager = Bootstrap::getObjectManager();
        $this->optimizationManager = $objectManager->create(OptimizationManagerInterface::class);
        $this->storeManager = $objectManager->get(StoreManagerInterface::class);
    }

    /**
     * @covers \Mageproxy\Connector\Model\OptimizationManager::deploy
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_ready.php
     */
    public function testDeployOptimization(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $optimizationManager = $objectManager->create(OptimizationManagerInterface::class);
        $optimization = $objectManager->get(OptimizationRepositoryInterface::class)->get('ready');
        $optimizationManager->deploy($optimization);
        self::assertSame(OptimizationInterface::STATUS_DEPLOYED, $optimization->getStatus());
    }

    /**
     * @covers \Mageproxy\Connector\Model\OptimizationManager::revert
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_deployed.php
     */
    public function testRevertOptimization(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $optimizationManager = $objectManager->create(OptimizationManagerInterface::class);
        $optimization = $objectManager->get(OptimizationRepositoryInterface::class)->get('deployed');
        $optimizationManager->revert($optimization);

        self::assertSame(OptimizationInterface::STATUS_READY, $optimization->getStatus());
    }

    /**
     * @covers \Mageproxy\Connector\Model\OptimizationManager::deploymentInProgress
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_deployed.php
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 1
     */
    public function testDeploymentInProgressForDefaultStore(): void
    {
        self::assertTrue(
            $this->optimizationManager->deploymentInProgress()
        );
    }

    /**
     * @covers \Mageproxy\Connector\Model\OptimizationManager::deploymentInProgress
     * @magentoDataFixture Magento/Store/_files/second_store.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_deployed.php
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 1
     */
    public function testDeployedInProgressForSpecificStore(): void
    {
        $optimization = Bootstrap::getObjectManager()
            ->get(OptimizationRepositoryInterface::class)
            ->get('deployed');
        $storeId = (int) $this->storeManager->getStore('fixture_second_store')->getId();
        $optimization->setStoreId($storeId);
        $optimization->save();
        self::assertTrue(
            $this->optimizationManager->deploymentInProgress($storeId)
        );
        self::assertFalse(
            $this->optimizationManager->deploymentInProgress(Store::DISTRO_STORE_ID)
        );
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_stopped.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_template_default_store.php
     * @magentoConfigFixture default/mageproxy_connector/settings/run_mode auto
     */
    public function testRequestWithTemplate(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $repository = $objectManager->create(OptimizationTemplateRepositoryInterface::class);
        $searchCriteriaBuilder = $objectManager->create(SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('store_id', Store::DEFAULT_STORE_ID)->create();
        $result = $repository->getList($searchCriteria);
        $items = $result->getItems();
        $template = array_shift($items);
        $recording = $objectManager->create(RecordingRepositoryInterface::class)->get('stopped');
        $recording->setInitiator(RecordingInterface::INITIATOR_CRON);

        $response = $objectManager->create(PostOptimizeRecordingResponseInterface::class);
        $response->setId('1231221');
        $apiClient = self::createMock(PostOptimizeRecordingInterface::class);
        $apiClient->expects(self::once())
            ->method('execute')
            ->willReturn($response);

        $optimizationManager = $objectManager->create(OptimizationManagerInterface::class, [
            'postOptimizeRecording' => $apiClient
        ]);
        $optimization = $optimizationManager->request($recording, 'cron', $template);
        self::assertSame('1231221', $optimization->getUuid());
        self::assertSame(false, $optimization->getMinifyHtml());
        self::assertSame(false, $optimization->getMinifyJs());
        self::assertSame(['default/dep1', 'default/dep2'], $optimization->getExcludeDeps());
        self::assertSame(['default/dep3', 'default/dep4'], $optimization->getRemoveDeps());
        self::assertSame(['default_handle1', 'default_handle2'], $optimization->getHandles());
        self::assertSame('defaults', $optimization->getBrowserslistConfig());
        self::assertSame(['Foo_Bar/**/*.js'], $optimization->getTranspileGlobs());
    }
}
