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
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use PHPUnit\Framework\TestCase;

class RecordingRepositoryTest extends TestCase
{
    /**
     * @var \Mageproxy\Connector\Api\RecordingRepositoryInterface|null
     */
    private $repository;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->repository = $objectManager->get(RecordingRepositoryInterface::class);
    }

    public function testSaveNewRecording()
    {
        $recording = $this->createNewRecording();
        $this->repository->save($recording);
        self::assertNotEmpty($recording->getId());
    }

    public function testCouldNotSaveRecording(): void
    {
        $this->expectException(CouldNotSaveException::class);

        $recording1 = $this->createNewRecording('uuid-test');
        $this->repository->save($recording1);

        $recording2 = $this->createNewRecording('uuid-test');
        $this->repository->save($recording2);
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recordings.php
     * @magentoConfigFixture default/mageproxy_connector/settings/run_mode manual
     */
    public function testGetList()
    {
        $objectManager = Bootstrap::getObjectManager();

        $searchCriteriaBuilder = $objectManager->get(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter('status', RecordingInterface::STATUS_PENDING);
        $searchCriteria = $searchCriteriaBuilder->create();

        $result = $this->repository->getList($searchCriteria);

        $items = $result->getItems();
        $this->assertCount(1, $items);
    }

    /**
     * @covers \Mageproxy\Connector\Model\RecordingRepository::deleteById
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_pending.php
     */
    public function testDeleteById(): void
    {
        $recording = $this->repository->get('pending');
        $result = $this->repository->deleteById((int) $recording->getId());
        self::assertTrue($result);
        self::expectException(NoSuchEntityException::class);
        $this->repository->getById((int) $recording->getId());
    }

    public function testDeleteByIdThrowsNoSuchEntityException(): void
    {
        $this->expectException(NoSuchEntityException::class);
        $this->repository->deleteById(999);
    }

    public function testDelete(): void
    {
        $recording = $this->createNewRecording();
        $this->repository->save($recording);
        $result = $this->repository->delete($recording);
        self::assertTrue($result);
        self::expectException(NoSuchEntityException::class);
        $this->repository->getById((int) $recording->getId());
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_pending.php
     * @covers \Mageproxy\Connector\Model\RecordingRepository::getById
     */
    public function testGetById(): void
    {
        $recording = $this->repository->get('pending');
        $result = $this->repository->getById((int) $recording->getId());
        self::assertNotEmpty($result->getId());
    }

    public function testGetByIdThrowsNoSuchEntityExceptionForUnknownId(): void
    {
        $this->expectException(NoSuchEntityException::class);
        $this->repository->getById(999);
    }

    /**
     * @covers \Mageproxy\Connector\Model\RecordingRepository::get
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_pending.php
     */
    public function testGet(): void
    {
        $recording = $this->repository->get('pending');
        self::assertNotEmpty($recording->getId());
    }

    /**
     * @covers \Mageproxy\Connector\Model\RecordingRepository::get
     */
    public function testGetThrowsNoSuchEntityException(): void
    {
        $this->expectException(NoSuchEntityException::class);
        $this->repository->get('non-existing-uuid');
    }

    private function createNewRecording(string $uuid = null): RecordingInterface
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $recording = $objectManager->create(RecordingInterface::class);
        $recording->setUuid($uuid ?? $objectManager->get(IdentityGeneratorInterface::class)->generateId());
        $recording->setStatus(RecordingInterface::STATUS_PENDING);
        $recording->setScheduledAt('2024-02-12 12:00:00');
        $recording->setStoreId(Store::DISTRO_STORE_ID);

        return $recording;
    }
}
