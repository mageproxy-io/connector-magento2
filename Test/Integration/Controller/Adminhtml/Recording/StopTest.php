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
use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\RecordingManagerInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;

/**
 * @covers \Mageproxy\Connector\Controller\Adminhtml\Recording\Stop::execute
 * @magentoAppArea adminhtml
 */
class StopTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    protected function setUp(): void
    {
        $this->resource = 'Mageproxy_Connector::recording_stop';
        $this->uri = 'backend/mageproxy/recording/stop';
        parent::setUp();
    }

    public function testAclHasAccess()
    {
        self::markTestSkipped('Tested in the following tests');
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_running.php
     */
    public function testInvokeControllerForStartedRecordingIsSuccessful(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $startedRecording = $objectManager->get(RecordingRepositoryInterface::class)->get('running');

        /** @var \Magento\TestFramework\Request $request */
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setParam('id', $startedRecording->getId());

        $recordingManagerMock = $this->createMock(RecordingManagerInterface::class);
        $recordingManagerMock
            ->expects(self::once())
            ->method('stop');
        $objectManager->addSharedInstance($recordingManagerMock, RecordingManagerInterface::class, true);

        $this->dispatch($this->uri);

        $this->assertRedirect(self::stringContains('backend/mageproxy/recording/view/id/' . $startedRecording->getId()));
        $this->assertSessionMessages(
            self::equalTo(['The recording was stopped successfully.']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertSessionMessages(self::isEmpty(), \Magento\Framework\Message\MessageInterface::TYPE_ERROR);
    }
}
