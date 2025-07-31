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
 * @covers \Mageproxy\Connector\Controller\Adminhtml\Recording\Index
 * @magentoAppArea adminhtml
 */
class IndexTest extends AbstractBackendController
{
    protected function setUp(): void
    {
        $this->resource = 'Mageproxy_Connector::recording_view';
        $this->uri = 'backend/mageproxy/recording/index';
        parent::setUp();
    }

    /**
     * @magentoConfigFixture default/mageproxy_connector/settings/enabled 0
     */
    public function testWarningMessageWhenModuleDisabled(): void
    {
        $this->dispatch($this->uri);
        $messages = $this->getMessages();
        $foo = 'bar';
    }
}
