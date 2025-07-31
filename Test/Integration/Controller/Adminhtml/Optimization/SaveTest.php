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

use Laminas\Http\Request;
use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Model\ApiClient\PostOptimizeRecordingInterface;
use Mageproxy\Connector\Model\ApiClient\PostOptimizeRecordingResponseInterface;

/**
 * @covers \Mageproxy\Connector\Controller\Adminhtml\Optimization\Save::execute
 * @magentoAppArea adminhtml
 */
class SaveTest extends AbstractBackendController
{
    protected function setUp(): void
    {
        $this->resource = 'Mageproxy_Connector::optimization_create';
        $this->uri = 'backend/mageproxy/optimization/save';
        parent::setUp();
    }

    public function testAclHasAccess()
    {
        $this->markTestSkipped('Subsequently tested with the tests below.');
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     */
    public function testSuccessfulSave(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        // Create POST body
        /** @var \Magento\TestFramework\Request $request */
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $recording = $objectManager->get(RecordingRepositoryInterface::class)->get('finished');
        $request->setPostValue([
            'recording_id' => $recording->getId(),
            'minify_js' => '1',
            'minify_html' => '1'
        ]);

        // Mock API adapter and response
        $apiResponse = $objectManager->create(PostOptimizeRecordingResponseInterface::class);
        $uuid = $objectManager->get(IdentityGeneratorInterface::class)->generateId();
        $apiResponse->setId($uuid);
        $optimizeRecordingApiPost = self::createMock(PostOptimizeRecordingInterface::class);
        $optimizeRecordingApiPost
            ->expects(self::once())
            ->method('execute')
            ->willReturn($apiResponse);
        $objectManager->addSharedInstance($optimizeRecordingApiPost, PostOptimizeRecordingInterface::class, true);

        $this->dispatch($this->uri);

        /** @var \Magento\TestFramework\Response $response */
        $response = $this->getResponse();

        $this->assertRedirect($this->stringContains('backend/mageproxy/recording/view'));
        $this->assertSessionMessages(
            self::stringContains(''),
            MessageInterface::TYPE_SUCCESS
        );
    }
}
