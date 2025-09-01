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

use Magento\Framework\HTTP\Client\Curl;
use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Model\ApiClient\Adapter;
use Mageproxy\Connector\Model\ApiClient\GetServiceResponseInterface;
use Mageproxy\Connector\Model\ApiClient\PostNewRecordingRequestInterface;
use Mageproxy\Connector\Model\ApiClient\PostNewRecordingResponseInterface;
use Mageproxy\Connector\Model\ApiClient\ResponseFactory;
use Mageproxy\Connector\Model\Config;
use PHPUnit\Framework\TestCase;

class AdapterTest extends TestCase
{
    public function testMethodDoesNotExistThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $objectManager = Bootstrap::getObjectManager();
        $client = $objectManager->create(Adapter::class);
        $client->foo('foo', 'bar');
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/api_key asdf1231adsf
     * @magentoConfigFixture default/mageproxy_connector/settings/api_base_url https://www.example.com
     * @magentoConfigFixture default/mageproxy_connector/settings/service_id 123123-1231231-12312
     */
    public function testPost(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        $serviceId = '123123-1231231-12312';
        $curlMock = self::createMock(Curl::class);
        $curlMock->expects(self::once())
            ->method('setHeaders')
            ->with([
                'Content-Type' => 'application/json',
                'X-Api-Key' => 'asdf1231adsf',
                'X-Service-Id' => $serviceId
            ]);
        $curlMock
            ->expects(self::once())
            ->method('post')
            ->with(
                "https://www.example.com/v1/services/$serviceId/record",
                '{"service_id":"123123-1231231-12312","domain":"example.com"}'
            );

        $curlMock->expects(self::once())
            ->method('getBody')
            ->willReturn('{"id":"uuid-recording"}');
        $curlMock->expects(self::once())
            ->method('getStatus')
            ->willReturn(200);

        $curlFactoryMock = self::createMock(\Mageproxy\Connector\Model\ApiClient\CurlFactory::class);
        $curlFactoryMock
            ->expects(self::once())
            ->method('create')
            ->willReturn($curlMock);

        $client = $objectManager->create(Adapter::class, [
            'endpointConfigPath' => Config::XML_PATH_API_PATH_NEW_RECORDING,
            'curlFactory' => $curlFactoryMock,
            'responseFactory' => $objectManager->create(ResponseFactory::class, [
                'instanceName' => PostNewRecordingResponseInterface::class
            ])
        ]);

        $requestObject = $objectManager->create(PostNewRecordingRequestInterface::class);
        $requestObject->setServiceId('123123-1231231-12312');
        $requestObject->setDomain('example.com');
        $response = $client->post($requestObject, ['id' => $serviceId]);
        self::assertSame('uuid-recording', $response->getId());
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/api_base_url https://www.example.com
     */
    public function testGet(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        $curlMock = self::createMock(Curl::class);
        $curlMock
            ->expects(self::once())
            ->method('get')
            ->with('https://www.example.com/v1/services/1232-1231-12312');
        $curlMock->expects(self::once())
            ->method('getBody')
            ->willReturn('{"mode":"test","plan":"free"}');

        $curlMock->expects(self::once())
            ->method('getStatus')
            ->willReturn(200);

        $curlFactoryMock = self::createMock(\Mageproxy\Connector\Model\ApiClient\CurlFactory::class);
        $curlFactoryMock
            ->expects(self::once())
            ->method('create')
            ->willReturn($curlMock);

        $client = $objectManager->create(Adapter::class, [
            'endpointConfigPath' => Config::XML_PATH_API_PATH_GET_SERVICE,
            'curlFactory' => $curlFactoryMock,
            'responseFactory' => $objectManager->create(ResponseFactory::class, [
                'instanceName' => GetServiceResponseInterface::class
            ])
        ]);

        $response = $client->get(['id' => '1232-1231-12312']);
        self::assertSame('test', $response->getMode());
        self::assertSame('free', $response->getPlan());
    }
}
