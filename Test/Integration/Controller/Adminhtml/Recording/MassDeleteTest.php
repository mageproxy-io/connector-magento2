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
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Mageproxy\Connector\Model\ApiClient\DeleteRecordingInterface;
use Mageproxy\Connector\Model\ResourceModel\Recording\Collection;

/**
 * @covers \Mageproxy\Connector\Controller\Adminhtml\Recording\Delete
 * @magentoAppArea adminhtml
 */
class MassDeleteTest extends AbstractBackendController
{
    protected function setUp(): void
    {
        $this->resource = 'Mageproxy_Connector::recording_delete';
        $this->uri = 'backend/mageproxy/recording/massDelete';
        $this->httpMethod = Request::METHOD_POST;
        parent::setUp();
    }

    public function testAclHasAccess()
    {
        $this->markTestSkipped('Subsequently tested with the tests below.');
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recordings.php
     * @magentoConfigFixture default/mageproxy_connector/settings/run_mode manual
     */
    public function testSuccessfulDispatch(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $recordingCollection = $objectManager->create(Collection::class);
        $items = $recordingCollection->getItems();
        $deleteIds = array_slice(array_keys($items), 0, 2);
        $expectedIdsAfterDelete = array_slice(array_keys($items), 1); // second recording is running
        $requestData = [
            'selected' => $deleteIds,
            'namespace' => 'mageproxy_recording_listing',
        ];

        $apiDeleteMock = $this->createMock(DeleteRecordingInterface::class);
        $apiDeleteMock->expects(self::exactly(1))
            ->method('execute');

        $objectManager->addSharedInstance($apiDeleteMock, DeleteRecordingInterface::class, true);

        $this->getRequest()
            ->setParams($requestData)
            ->setMethod($this->httpMethod);
        $this->dispatch($this->uri);

        $recordingCollection->clear();
        $idsAfterDelete = $recordingCollection->getAllIds();
        self::assertEquals($idsAfterDelete, $expectedIdsAfterDelete);
        $this->assertSessionMessages(
            $this->equalTo(['A total of 1 record(s) have been deleted.']),
            MessageInterface::TYPE_SUCCESS
        );
    }

}
