<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */

declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Model\System\Config\Source;

use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Model\System\Config\Source\Duration;
use PHPUnit\Framework\TestCase;

class DurationTest extends TestCase
{
    public function testToOptionArray(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $durationSource = $objectManager->create(Duration::class);
        $options = $durationSource->toOptionArray();
        self::assertCount(6, $options);
    }
}
