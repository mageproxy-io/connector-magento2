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

use Mageproxy\Connector\Model\ApiClient\PostNewRecordingResponseInterface;
use Mageproxy\Connector\Model\ApiClient\PostNewRecordingResponseInterfaceFactory;
use PHPUnit\Framework\TestCase;

class PostNewRecordingResponseTest extends TestCase
{
    private ?PostNewRecordingResponseInterfaceFactory $factory = null;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->factory = $objectManager->get(PostNewRecordingResponseInterfaceFactory::class);
    }

    public function testGetId(): void
    {
        $response = $this->factory->create();
        $response->setId('1123-1231313-12313');
        self::assertSame('1123-1231313-12313', $response->getId());
    }

    public function testSetId(): void
    {
        $response = $this->factory->create();
        $response->setId('1123-1231313-12313');
        self::assertSame('1123-1231313-12313', $response->getId());
    }

    public function testGetDefaultPageHandlePriority(): void
    {
        $response = $this->factory->create();
        $response->setDefaultPageHandlePriority(['page1', 'page2']);
        self::assertSame(['page1', 'page2'], $response->getDefaultPageHandlePriority());
    }

    public function testPopulateFromResponse(): void
    {
        $responsePayload = [
            'id' => 'ae50dba8-a670-4c87-9ef0-135747ac6bc8',
            'default_page_handle_priority' => ['home', 'catalog']
        ];

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $dataObjectHelper = $objectManager->get(\Magento\Framework\Api\DataObjectHelper::class);
        $response = $this->factory->create();
        $dataObjectHelper->populateWithArray(
            $response,
            $responsePayload,
            PostNewRecordingResponseInterface::class
        );
        self::assertSame('ae50dba8-a670-4c87-9ef0-135747ac6bc8', $response->getId());
        self::assertSame(['home', 'catalog'], $response->getDefaultPageHandlePriority());
    }
}
