<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Controller\Adminhtml\Recording;

use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @covers \Mageproxy\Connector\Controller\Adminhtml\Recording\Create::execute
 * @magentoAppArea adminhtml
 */
class CreateTest extends AbstractBackendController
{
    protected function setUp(): void
    {
        $this->resource = 'Mageproxy_Connector::recording_create';
        $this->uri = 'backend/mageproxy/recording/create';
        parent::setUp();
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 0
     */
    public function testInvokeControllerWhenFeatureFlagDisabledRedirects(): void
    {
        $this->dispatch($this->uri);
        $this->assertRedirect($this->stringContains('backend/mageproxy/recording/index'));
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 1
     */
    public function testInvokeControllerWhenFeatureFlagEnabledReturnsPage(): void
    {
        /** @var \Magento\TestFramework\Response $response */
        $response = $this->getResponse();
        $this->dispatch($this->uri);
        self::assertEquals(200, $response->getHttpResponseCode());
    }
}
