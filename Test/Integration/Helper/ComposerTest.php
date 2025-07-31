<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Test\Integration\Helper;

use Magento\TestFramework\Helper\Bootstrap;
use Mageproxy\Connector\Helper\Composer;
use PHPUnit\Framework\TestCase;

class ComposerTest extends TestCase
{
    /**
     * @var \Mageproxy\Connector\Helper\Composer
     */
    private $composerHelper;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->composerHelper = Bootstrap::getObjectManager()
            ->get(Composer::class);
    }

    public function testGetVersion(): void
    {
        $version = $this->composerHelper->getVersion();
        $this->assertNotEmpty($version, 'Extension version should not be empty');
        $this->assertIsString($version, 'Extension version should be a string');
    }
}
