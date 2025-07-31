<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Controller\Adminhtml\Optimization;

use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @covers \Mageproxy\Connector\Controller\Adminhtml\Optimization\Create::execute
 * @magentoAppArea adminhtml
 */
class CreateTest extends AbstractBackendController
{
    protected function setUp(): void
    {
        $this->resource = 'Mageproxy_Connector::optimization_create';
        $this->uri = 'backend/mageproxy/optimization/create';
        parent::setUp();
    }

    public function testExecute(): void
    {
        /** @var \Magento\TestFramework\Request $request */
        $request = $this->getRequest();
        $request->setMethod('GET');
        $this->dispatch($this->uri);
        /** @var \Magento\TestFramework\Response $response */
        $response = $this->getResponse();
        self::assertEquals(200, $response->getHttpResponseCode());
    }
}
