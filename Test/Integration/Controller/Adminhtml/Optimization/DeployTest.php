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
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\OptimizationManagerInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;

/**
 * @covers \Mageproxy\Connector\Controller\Adminhtml\Optimization\Deploy::execute
 * @magentoAppArea adminhtml
 */
class DeployTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    protected function setUp(): void
    {
        $this->resource = 'Mageproxy_Connector::optimization_deploy';
        $this->uri = 'backend/mageproxy/optimization/deploy';
        parent::setUp();
    }

    public function testAclHasAccess()
    {
        $this->markTestSkipped('Subsequently tested with the tests below.');
    }

    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_finished.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_ready.php
     */
    public function testExecute()
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $optimization = $objectManager->get(OptimizationRepositoryInterface::class)->get('ready');

        /** @var \Magento\TestFramework\Request $request */
        $request = $this->getRequest();
        $request->setParam('optimization_id', $optimization->getId());
        $request->setMethod(Request::METHOD_GET);

        $optimizationManager = self::createMock(OptimizationManagerInterface::class);
        $optimizationManager
            ->expects(self::once())
            ->method('deploy');
        $objectManager->addSharedInstance($optimizationManager, OptimizationManagerInterface::class, true);

        // Invoke the controller
        $this->dispatch($this->uri);

        $this->assertRedirect(self::stringContains('backend/mageproxy/recording/view'));
        $escaper = $objectManager->get(\Magento\Framework\Escaper::class);
        $successMessage = (string) __('The optimization was deployed. Select "Revert" do undo the deployment.');
        $this->assertSessionMessages(
            self::equalTo([$escaper->escapeHtml($successMessage)]),
            MessageInterface::TYPE_SUCCESS
        );
    }
}
