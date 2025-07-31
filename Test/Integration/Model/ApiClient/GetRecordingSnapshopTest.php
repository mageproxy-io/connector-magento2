<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\ApiClient;

use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Model\ApiClient\Adapter;
use Mageproxy\Connector\Model\ApiClient\GetRecordingSnapshotInterface;
use Mageproxy\Connector\Model\ApiClient\GetRecordingSnapshotResponseInterface;
use PHPUnit\Framework\TestCase;

class GetRecordingSnapshopTest extends TestCase
{
    public function testGetRecordingSnapshop(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        $adapterMock = $this->getMockBuilder(Adapter::class)
            ->addMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();

        $uuid = '123e4567-e89b-12d3-a456-426614174000';

        $response = $objectManager->create(GetRecordingSnapshotResponseInterface::class);
        $response->setId($uuid);

        $adapterMock->expects(self::once())
            ->method('get')
            ->with(['id' => '123e4567-e89b-12d3-a456-426614174000'])
            ->willReturn($response);

        $client = $objectManager->create(GetRecordingSnapshotInterface::class, [
            'adapter' => $adapterMock
        ]);

        $response = $client->execute($uuid);

        self::assertSame($uuid, $response->getId());
    }
}
