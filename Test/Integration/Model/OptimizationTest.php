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

use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\Data\OptimizationInterfaceFactory;
use Mageproxy\Connector\Model\Recording;
use Mageproxy\Connector\Test\Integration\Framework\OptimizationFixtureResolver;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Mageproxy\Connector\Model\Optimization
 */
class OptimizationTest extends TestCase
{
    private ?OptimizationInterfaceFactory $optimizationFactory = null;

    protected function setUp(): void
    {
        parent::setUp();
        $objectManager = Bootstrap::getObjectManager();
        $this->optimizationFactory = $objectManager->get(OptimizationInterfaceFactory::class);
    }

    /**
     * @covers \Mageproxy\Connector\Model\Optimization::getMinifyHtml
     */
    public function testGetMinifyHtml(): void
    {
        $optimization = $this->optimizationFactory->create();
        $optimization->setData('minify_html', true);
        self::assertTrue($optimization->getMinifyHtml());
    }

    /**
     * @covers \Mageproxy\Connector\Model\Optimization::setMinifyHtml
     */
    public function testSetMinifyHtml(): void
    {
        $optimization = $this->optimizationFactory->create();
        $optimization->setMinifyHtml(true);
        self::assertTrue($optimization->getData('minify_html'));
    }

    /**
     * @covers \Mageproxy\Connector\Model\Optimization::getMinifyJs
     */
    public function testGetMinifyJs(): void
    {
        $optimization = $this->optimizationFactory->create();
        $optimization->setData('minify_js', true);
        self::assertTrue($optimization->getMinifyJs());
    }

    /**
     * @covers \Mageproxy\Connector\Model\Optimization::setMinifyJs
     */
    public function testSetMinifyJs(): void
    {
        $optimization = $this->optimizationFactory->create();
        $optimization->setMinifyJs(true);
        self::assertTrue($optimization->getData('minify_js'));
    }

    /**
     * @covers \Mageproxy\Connector\Model\Optimization::getUuid
     */
    public function testGetUuid(): void
    {
        $optimization = $this->optimizationFactory->create();
        $optimization->setData('uuid', 'test');
        self::assertSame('test', $optimization->getUuid());
    }

    /**
     * @covers \Mageproxy\Connector\Model\Optimization::setUuid
     */
    public function testSetUuid(): void
    {
        $optimization = $this->optimizationFactory->create();
        $optimization->setUuid('test');
        self::assertSame('test', $optimization->getData('uuid'));
    }

    /**
     * @covers \Mageproxy\Connector\Model\Optimization::getStoreId
     */
    public function testGetStoreId(): void
    {
        $optimization = $this->optimizationFactory->create();
        $optimization->setData('store_id', 2);
        self::assertSame(2, $optimization->getStoreId());
    }

    /**
     * @covers \Mageproxy\Connector\Model\Optimization::setStoreId
     */
    public function testSetStoreId(): void
    {
        $optimization = $this->optimizationFactory->create();
        $optimization->setStoreId(2);
        self::assertSame(2, $optimization->getData('store_id'));
    }

    /**
     * @covers \Mageproxy\Connector\Model\Optimization::setRequestedAt
     */
    public function testSetRequestedAt(): void
    {
        $optimization = $this->optimizationFactory->create();
        $optimization->setRequestedAt('2024-01-01 00:00:00');
        self::assertSame('2024-01-01 00:00:00', $optimization->getData('requested_at'));
    }

    /**
     * @covers \Mageproxy\Connector\Model\Optimization::getRequestedAt
     */
    public function testGetRequestedAt(): void
    {
        $optimization = $this->optimizationFactory->create();
        $optimization->setData('requested_at', '2024-01-01 00:00:00');
        self::assertSame('2024-01-01 00:00:00', $optimization->getRequestedAt());
    }

    /**
     * @covers \Mageproxy\Connector\Model\Optimization::getRecordingId
     */
    public function testGetRecordingId(): void
    {
        $optimization = $this->optimizationFactory->create();
        $optimization->setData('recording_id', 5);
        self::assertSame(5, $optimization->getRecordingId());
    }

    /**
     * @covers \Mageproxy\Connector\Model\Optimization::setRecordingId
     */
    public function testSetRecordingId(): void
    {
        $optimization = $this->optimizationFactory->create();
        $optimization->setRecordingId(10);
        self::assertSame(10, $optimization->getData('recording_id'));
    }

    /**
     * @covers \Mageproxy\Connector\Model\Optimization::getStatus
     */
    public function testGetStatus(): void
    {
        $optimization = $this->optimizationFactory->create();
        $optimization->setData('status', 4);
        self::assertSame(4, $optimization->getStatus());
    }

    /**
     * @covers \Mageproxy\Connector\Model\Optimization::setStatus
     */
    public function testSetStatus(): void
    {
        $optimization = $this->optimizationFactory->create();
        $optimization->setStatus(3);
        self::assertSame(3, $optimization->getData('status'));
    }

    /**
     * @covers \Mageproxy\Connector\Model\Optimization::getRecording
     */
    public function testGetRecordingIsNullable(): void
    {
        $optimization = $this->optimizationFactory->create();
        self::assertNull($optimization->getRecording());
    }

    /**
     * @covers \Mageproxy\Connector\Model\Optimization::getRecording
     * @covers \Mageproxy\Connector\Model\Optimization::setRecording
     */
    public function testGetSetRecording(): void
    {
        $optimization = $this->optimizationFactory->create();
        $recording = $this->createMock(Recording::class);
        $recording->expects(self::once())->method('getId')->willReturn(1);
        $optimization->setRecording($recording);
        self::assertSame($recording, $optimization->getRecording());
    }
}
