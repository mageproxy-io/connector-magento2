<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\Recording\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Mageproxy\Connector\Model\Recording\Source\Status;

class TestStatus extends TestCase
{
    private $objectManager;
    private Status $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->objectManager = Bootstrap::getObjectManager();
        $this->sut = $this->objectManager->get(Status::class);
    }

    public function testImplementsOptionSourceInterface(): void
    {
        $this->assertInstanceOf(OptionSourceInterface::class, $this->sut);
    }

    public function testToOptionArray(): void
    {
        $options = $this->sut->toOptionArray();
        $this->assertNotEmpty($options);
    }
}
