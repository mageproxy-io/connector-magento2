<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\Config;

use Magento\Framework\App\State;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Asset\Minification;
use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Model\ApiClient\GetServiceInterface;
use Mageproxy\Connector\Model\ApiClient\GetServiceResponseInterface;
use Mageproxy\Connector\Model\Config\DeploymentModeValidator;
use Mageproxy\Connector\Model\Config\DevJsConfigValidator;
use Mageproxy\Connector\Model\Config\ServiceIdValidator;
use Mageproxy\Connector\Model\ConfigValidator;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea adminhtml
 */
class ConfigValidatorTest extends TestCase
{
    /**
     * @magentoConfigFixture default_store dev/js/minify_files 1
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 1
     */
    public function testConfigValidatorReturnsErrorWhenMinificationEnabled(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();
        $state = $objectManager->create(State::class, [
            'mode' => 'production',
        ]);
        $minification = $objectManager->create(Minification::class, [
            'appState' => $state
        ]);
        $validator = $objectManager->create(DevJsConfigValidator::class, [
            'minification' => $minification,
        ]);
        $configValidator = $objectManager->create(ConfigValidator::class, [
            'validators' => ['devJsConfigValidator' => $validator],
        ]);
        $errors = $configValidator->validate();
        self::assertNotEmpty($errors);
    }

    /**
     * @magentoConfigFixture default_store dev/js/enable_js_bundling 1
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 1
     */
    public function testConfigValidatorReturnsErrorWhenBundlingIsEnabled(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $validator = $objectManager->create(DevJsConfigValidator::class);
        $configValidator = $objectManager->create(ConfigValidator::class, [
            'validators' => ['devJsConfigValidator' => $validator],
        ]);
        $errors = $configValidator->validate();

        self::assertNotEmpty($errors);
    }

    /**
     * @magentoConfigFixture default_store dev/js/merge_files 1
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 1
     */
    public function testConfigValidatorReturnsErrorWhenJsMergingIsEnabled(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $validator = $objectManager->create(DevJsConfigValidator::class);
        $configValidator = $objectManager->create(ConfigValidator::class, [
            'validators' => ['devJsConfigValidator' => $validator],
        ]);
        $errors = $configValidator->validate();

        self::assertNotEmpty($errors);
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 1
     */
    public function testConfigValidatorReturnsFalseWithCompactSCDMode(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $rjsConfigMock = self::createMock(\Magento\Framework\RequireJs\Config::class);
        $rjsConfigMock
            ->expects(self::once())
            ->method('getMapFileRelativePath')
            ->willReturn('path/to/map.js');

        $writeDirMock = self::createMock(\Magento\Framework\Filesystem\Directory\WriteInterface::class);
        $writeDirMock
            ->expects(self::once())
            ->method('isExist')
            ->with('path/to/map.js')
            ->willReturn(true);

        $fileSystemMock = self::createMock(\Magento\Framework\Filesystem::class);
        $fileSystemMock
            ->expects(self::once())
            ->method('getDirectoryWrite')
            ->willReturn($writeDirMock);

        $validator = $objectManager->create(DeploymentModeValidator::class, [
            'rjsConfig' => $rjsConfigMock,
            'filesystem' => $fileSystemMock
        ]);

        $configValidator = $objectManager->create(ConfigValidator::class, [
            'validators' => ['deploymentMode' => $validator],
        ]);

        $errors = $configValidator->validate();

        self::assertNotEmpty($errors);
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 1
     * @magentoConfigFixture default/mageproxy_connector/settings/service_id 123
     */
    public function testConfigValidatorReturnsErrorWhenServiceIdUnknown(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $getServiceMock = self::createMock(GetServiceInterface::class);
        $getServiceResponse = $objectManager->create(GetServiceResponseInterface::class);
        $getServiceResponse->setStatus('pending');
        $getServiceMock
            ->expects(self::once())
            ->method('execute')
            ->willReturn($getServiceResponse);

        $validator = $objectManager->create(ServiceIdValidator::class, [
            'getServiceApiClient' => $getServiceMock,
        ]);
        $configValidator = $objectManager->create(ConfigValidator::class, [
            'validators' => ['serviceId' => $validator],
        ]);
        $result = $configValidator->validate();

        self::assertNotEmpty($result['errors']);
        self::assertSame('The service ID is not active. Please contact support.', (string) $result['errors'][0]);
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 1
     * @magentoConfigFixture default/mageproxy_connector/settings/service_id 123
     * @magentoAppArea adminhtml
     */
    public function testConfigValidatorFailsWhenServiceIdDoesNotExist(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $getServiceMock = self::createMock(GetServiceInterface::class);
        $getServiceMock
            ->expects(self::once())
            ->method('execute')
            ->willThrowException(
                new NotFoundException(__('The service ID is invalid. Please contact support'))
            );

        $validator = $objectManager->create(ServiceIdValidator::class, [
            'getServiceApiClient' => $getServiceMock,
        ]);
        $configValidator = $objectManager->create(ConfigValidator::class, [
            'validators' => ['serviceId' => $validator],
        ]);
        $result = $configValidator->validate();

        self::assertSame('The service ID is invalid. Please contact support', (string) $result['errors'][0]);
    }
}
