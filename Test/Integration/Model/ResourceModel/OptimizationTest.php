<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\ResourceModel;

use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Model\Optimization;
use Mageproxy\Connector\Model\ResourceModel\Optimization as OptimizationResourceModel;
use PHPUnit\Framework\TestCase;

class OptimizationTest extends TestCase
{
    private $objectManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->objectManager = Bootstrap::getObjectManager();
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_running.php
     */
    public function testSaveModel()
    {
        $recording = $this->objectManager->get(RecordingRepositoryInterface::class)->get('running');

        $optimizationResourceModel = $this->objectManager->get(OptimizationResourceModel::class);
        $optimizationModel = $this->objectManager->get(Optimization::class);

        $optimizationModel->setRecording($recording);

        $uuid = $this->objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $optimizationModel->setUuid($uuid);
        $optimizationModel->setMinifyHtml(true);
        $optimizationModel->setMinifyJs(false);
        $optimizationModel->setRequestedAt('2024-02-25 12:00:00');
        $optimizationModel->setStatus(OptimizationInterface::STATUS_PUBLISHED);
        $optimizationModel->setStoreId(1);
        $optimizationResourceModel->save($optimizationModel);

        self::assertNotEmpty($optimizationModel->getId());

        $loadedModel = $this->objectManager->create(OptimizationInterface::class);
        $loadedModel->load($optimizationModel->getId());

        self::assertSame('2024-02-25 12:00:00', $loadedModel->getRequestedAt());
        self::assertSame(OptimizationInterface::STATUS_PUBLISHED, $loadedModel->getStatus());
        self::assertSame((int) $recording->getId(), $loadedModel->getRecordingId());
        self::assertSame($uuid, $loadedModel->getUuid());
        self::assertTrue($loadedModel->getMinifyHtml());
        self::assertFalse($loadedModel->getMinifyJs());
        self::assertSame(1, $loadedModel->getStoreId());
    }
}
