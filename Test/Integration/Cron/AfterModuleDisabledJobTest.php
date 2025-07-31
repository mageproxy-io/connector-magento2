<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Cron;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class AfterModuleDisabledJobTest extends TestCase
{
    /**
     * @magentoAppArea crontab
     */
    public function testItContainsTheExpectedProcessors(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $cronTask = $objectManager->get('Mageproxy\Connector\Cron\AfterModuleInactiveJob');
        $processors = $cronTask->getProcessors();
        $this->assertCount(2, $processors);
        $processors = array_values($processors);
        $this->assertInstanceOf('Mageproxy\Connector\Cron\Processor\RevertOptimization', $processors[0]);
        $this->assertInstanceOf('Mageproxy\Connector\Cron\Processor\FinishRecording', $processors[1]);
    }
}
