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
use Mageproxy\Connector\Model\Config as ModuleConfig;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private ?ModuleConfig $configModel = null;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->configModel = $objectManager->get(ModuleConfig::class);
    }

    /**
     * @covers \Mageproxy\Connector\Model\Config::getIsEnabled
     */
    public function testFeatureFlagDisabledByDefault(): void
    {
        self::assertFalse($this->configModel->getIsEnabled());
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 1
     * @magentoAppArea frontend
     * @covers \Mageproxy\Connector\Model\Config::getIsEnabled
     */
    public function testFeatureFlagEnabledWhenEnabledInSystemConfig(): void
    {
        self::assertTrue($this->configModel->getIsEnabled());
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 0
     * @covers \Mageproxy\Connector\Model\Config::getIsEnabled
     */
    public function testFeatureFlagDisabledWhenDisabledInSystemConfig(): void
    {
        self::assertFalse($this->configModel->getIsEnabled());
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/service_id service-id-here
     * @covers \Mageproxy\Connector\Model\Config::getServiceId
     */
    public function testGetServiceId(): void
    {
        self::assertEquals('service-id-here', $this->configModel->getServiceId());
    }

    /**
     * @covers \Mageproxy\Connector\Model\Config::getTrackingUrl
     */
    public function testGetDefaultTrackingUrlIsNull(): void
    {
        self::assertEquals(null, $this->configModel->getTrackingUrl());
    }

    /**
     * @covers \Mageproxy\Connector\Model\Config::getTrackingUrl
     * @magentoConfigFixture default/mageproxy_connector/settings/tracking_url https://recorder.mageproxy.io/v2/track
     */
    public function testGetTrackingUrlWhenSetInSystemConfig(): void
    {
        self::assertEquals('https://recorder.mageproxy.io/v2/track', $this->configModel->getTrackingUrl());
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/client_id client-id
     */
    public function testGetClientId(): void
    {
        self::assertEquals('client-id', $this->configModel->getClientId());
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/run_mode manual
     */
    public function testGetRunMode(): void
    {
        self::assertEquals('manual', $this->configModel->getRunMode());
    }

    public function testGetDefaultRunMode(): void
    {
        self::assertEquals('manual', $this->configModel->getRunMode());
    }

    /**
     * @magentoConfigFixture default_store mageproxy_connector/settings/auto_run_type continuous
     */
    public function testGetAutoRunType(): void
    {
        self::assertEquals('continuous', $this->configModel->getAutoRunType());
    }

    /**
     * @magentoConfigFixture default_store mageproxy_connector/settings/auto_run_duration 1400
     */
    public function testGetAutoRunDuration(): void
    {
        self::assertEquals(1400, $this->configModel->getAutoRunDuration());
    }

    /**
     * @magentoConfigFixture default_store mageproxy_connector/settings/auto_run_opt_freq 20
     */
    public function testGetAutoRunOptimizationFrequency(): void
    {
        self::assertEquals(20, $this->configModel->getAutoRunOptimizationFrequency());
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/api_base_url https://api-eu.mageproxy.io
     */
    public function testGetApiEndpoint(): void
    {
        $endpoint = $this->configModel->getApiEndpoint(ModuleConfig::XML_PATH_API_PATH_NEW_RECORDING);
        self::assertSame('https://api-eu.mageproxy.io/v1/services/:id/record', $endpoint);
        $endpoint = $this->configModel->getApiEndpoint(ModuleConfig::XML_PATH_API_PATH_GET_OPTIMIZATION, ['id' => 'uuid-1']);
        self::assertSame('https://api-eu.mageproxy.io/v1/optimizations/uuid-1', $endpoint);
        $endpoint = $this->configModel->getApiEndpoint(ModuleConfig::XML_PATH_API_PATH_OAUTH_TOKEN);
        self::assertSame('https://api-eu.mageproxy.io/v1/oauth/token', $endpoint);
        $endpoint = $this->configModel->getApiEndpoint(ModuleConfig::XML_PATH_API_PATH_GET_RECORDING_DEPS, ['id' => 'uuid-1']);
        self::assertSame('https://api-eu.mageproxy.io/v1/recordings/uuid-1/handles/deps', $endpoint);
        $endpoint = $this->configModel->getApiEndpoint(ModuleConfig::XML_PATH_API_PATH_RECORDING_OPTIMIZE, ['id' => 'uuid-1']);
        self::assertSame('https://api-eu.mageproxy.io/v1/recordings/uuid-1/optimize', $endpoint);
        $endpoint = $this->configModel->getApiEndpoint(ModuleConfig::XML_PATH_API_PATH_GET_RECORDING_SNAPSHOT, ['id' => 'uuid-1']);
    }

    /**
     * @magentoConfigFixture default_store mageproxy_connector/settings/preload_bundles 1
     */
    public function testGetPreloadConfigFlagTrueWhenEnabled(): void
    {
        self::assertTrue($this->configModel->getPreloadBundles());
    }
}
