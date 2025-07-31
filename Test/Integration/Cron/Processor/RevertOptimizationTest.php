<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Cron\Processor;

use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Cron\Processor\RevertOptimization;
use PHPUnit\Framework\TestCase;

class RevertOptimizationTest extends TestCase
{
    /**
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/recording_stopped.php
     * @magentoDataFixture Mageproxy_Connector::Test/Integration/_files/optimization_deployed.php
     * @magentoAppArea crontab
     */
    public function testItRevertsOptimization()
    {
        $objectManager = Bootstrap::getObjectManager();
        $repository = $objectManager->create(OptimizationRepositoryInterface::class);
        $optimization = $repository->get('deployed');
        $revertOptimizationCronProcessor = $objectManager->create(RevertOptimization::class);
        $revertOptimizationCronProcessor->process($optimization);
        $this->assertEquals(OptimizationInterface::STATUS_FINISHED, $optimization->getStatus());
    }
}
