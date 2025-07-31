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

use Laminas\Http\Request;
use Magento\Framework\Message\MessageInterface;

/**
 * @covers \Mageproxy\Connector\Controller\Adminhtml\Recording\Start
 * @magentoAppArea adminhtml
 */
class StartTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    protected function setUp(): void
    {
        $this->resource = 'Mageproxy_Connector::recording_start';
        $this->uri = 'backend/mageproxy/recording/start';
        parent::setUp();
    }

    public function testDispatchWithoutRecordingId()
    {
        $this->getRequest()->setMethod(Request::METHOD_GET);
        $this->dispatch($this->uri);

        $this->assertSessionMessages(
            self::equalTo(['The recording ID is missing.']),
            MessageInterface::TYPE_ERROR
        );
    }
}
