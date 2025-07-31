<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\Optimization\Source;

use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Model\Optimization\Source\Status;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Mageproxy\Connector\Model\Optimization\Source\Status
 */
class StatusTest extends TestCase
{
    public function testToOptionArray(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $options = $objectManager->get(Status::class)
            ->toOptionArray();

        foreach ($options as $option) {
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
            if ($option['value'] === OptimizationInterface::STATUS_REQUESTED) {
                $this->assertEquals('Requested', $option['label']);
            } elseif ($option['value'] === OptimizationInterface::STATUS_PUBLISHED) {
                $this->assertEquals('Published', $option['label']);
            } elseif ($option['value'] === OptimizationInterface::STATUS_READY) {
                $this->assertEquals('Ready', $option['label']);
            } elseif ($option['value'] === OptimizationInterface::STATUS_FAILED) {
                $this->assertEquals('Failed', $option['label']);
            } elseif ($option['value'] === OptimizationInterface::STATUS_DEPLOYED) {
                $this->assertEquals('Deployed', $option['label']);
            } elseif ($option['value'] === OptimizationInterface::STATUS_FINISHED) {
                $this->assertEquals('Finished', $option['label']);
            } else {
                $this->fail('Unknown status value');
            }
        }
    }
}
