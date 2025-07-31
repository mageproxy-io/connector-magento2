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
use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Model\ResourceModel\Optimization as ResourceModel;
use PHPUnit\Framework\TestCase;

class OptimizationRepositoryTest extends TestCase
{
    /**
     * @var \Mageproxy\Connector\Api\OptimizationRepositoryInterface|null
     */
    private $repository;

    /**
     * @var \Mageproxy\Connector\Model\ResourceModel\Optimization|null
     */
    private $resourceModel;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->repository = $objectManager->create(OptimizationRepositoryInterface::class);
        $this->resourceModel = $objectManager->create(ResourceModel::class);
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimizations.php
     */
    public function testGetList(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $searchCriteria = $objectManager->get(SearchCriteriaBuilder::class)->create();

        $result = $this->repository->getList($searchCriteria);
        $this->assertSame(6, $result->getTotalCount());
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     */
    public function testGetById(): void
    {
        $optimization = $this->createNewOptimization((int) $this->getRecording()->getId());
        $this->resourceModel->save($optimization);
        $actual = $this->repository->getById((int) $optimization->getId());
        $this->assertSame($optimization->getId(), $actual->getId());
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     */
    public function testGet(): void
    {
        $optimization = $this->createNewOptimization((int) $this->getRecording()->getId(), 'test-uuid');
        $this->resourceModel->save($optimization);
        $actual = $this->repository->get('test-uuid');
        $this->assertSame($optimization->getId(), $actual->getId());
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     */
    public function testSave(): void
    {
        $optimization = $this->createNewOptimization((int) $this->getRecording()->getId());
        $this->repository->save($optimization);
        $this->assertNotNull($optimization->getId());
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     */
    public function testUpdate(): void
    {
        $optimization = $this->createNewOptimization((int) $this->getRecording()->getId());
        $this->resourceModel->save($optimization);

        $optimization->setMinifyHtml(false);
        $optimization->setMinifyJs(false);

        $this->repository->save($optimization);

        $actual = $this->repository->getById((int) $optimization->getId());

        $this->assertFalse($actual->getMinifyHtml());
        $this->assertFalse($actual->getMinifyJs());
    }

    public function testCouldNotSave(): void
    {
        $this->expectException(CouldNotSaveException::class);

        $invalidRecordingId = 0;
        $optimization = $this->createNewOptimization($invalidRecordingId);

        $this->repository->save($optimization);
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     */
    public function testDeleteById(): void
    {
        $this->expectException(NoSuchEntityException::class);

        $optimization = $this->createNewOptimization((int) $this->getRecording()->getId());
        $this->resourceModel->save($optimization);
        $this->repository->deleteById((int) $optimization->getId());
        $this->repository->getById((int) $optimization->getId());
    }

    private function getRecording(): RecordingInterface
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $recordingRepo = $objectManager->create(RecordingRepositoryInterface::class);
        return $recordingRepo->get('finished');
    }

    private function createNewOptimization(int $recordingId, string $uuid = null): OptimizationInterface
    {
        $objectManager = Bootstrap::getObjectManager();
        $uuid = $uuid ?? $objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $optimization = $objectManager->create(OptimizationInterface::class);
        $optimization->setMinifyHtml(true);
        $optimization->setMinifyJs(true);
        $optimization->setRecordingId($recordingId);
        $optimization->setStatus(0);
        $optimization->setUuid($uuid);
        $optimization->setStoreId(1);
        $optimization->setRequestedAt('2024-01-01 00:00:00');
        return $optimization;
    }
}
