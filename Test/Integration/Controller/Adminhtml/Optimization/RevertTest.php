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
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Mageproxy\Connector\Api\OptimizationManagerInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;

/**
 * @covers \Mageproxy\Connector\Controller\Adminhtml\Optimization\Revert::execute
 * @magentoAppArea adminhtml
 */
class RevertTest extends AbstractBackendController
{

    protected function setUp(): void
    {
        $this->uri = '/backend/mageproxy/optimization/revert';
        $this->resource = 'Mageproxy_Connector::optimization_revert';
        parent::setup();
    }

    public function testAclHasAccess()
    {
        $this->markTestSkipped('Subsequently tested with the tests below.');
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_deployed.php
     */
    public function testRevertDeployedOptimization(): void
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $recording = $objectManager->get(RecordingRepositoryInterface::class)->get('finished');
        $optimization = $objectManager->get(OptimizationRepositoryInterface::class)->get('deployed');

        $optimizationManagerMock = self::createMock(OptimizationManagerInterface::class);
        $optimizationManagerMock->expects(self::once())
            ->method('revert');

        $objectManager->addSharedInstance(
            $optimizationManagerMock,
            OptimizationManagerInterface::class,
            true
        );

        $this->getRequest()->setMethod(Request::METHOD_GET);
        $this->getRequest()->setParams([
            'recording_id' => $recording->getId(),
            'optimization_id' => $optimization->getId()
        ]);
        $this->dispatch($this->uri);
        $this->assertRedirect(
            $this->stringContains('backend/mageproxy/recording/view/id/' . $recording->getId())
        );

    }
}
