<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Helper;

use Magento\Framework\HTTP\ClientFactory;
use Magento\Framework\HTTP\ClientInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Helper\Github;
use PHPUnit\Framework\TestCase;

class GithubTest extends TestCase
{
    public function testGetLatestVersion(): void
    {
        $clientMock = $this->createMock(ClientInterface::class);
        $clientMock->expects($this->once())
            ->method('getStatus')
            ->willReturn(200);
        $clientMock->expects($this->once())
            ->method('getBody')
            ->willReturn('{ "tag_name": "3.1.2" }');

        $clientFactoryMock = $this->createMock(ClientFactory::class);
        $clientFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($clientMock);

        $sut = Bootstrap::getObjectManager()->create(Github::class, [
            'clientFactory' => $clientFactoryMock
        ]);
        $release = $sut->getLatestRelease();
        $this->assertSame('3.1.2', $release);
    }
}
