<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */

declare(strict_types=1);

namespace Mageproxy\Connector\Model;

use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Model\System\Config\Source\RunMode;

class AutoRunModeActive
{
    /**
     * @var \Mageproxy\Connector\Model\Config
     */
    private Config $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    public function execute(RecordingInterface $recording): bool
    {
        return $this->config->getRunMode($recording->getStoreId()) === RunMode::MODE_AUTO;
    }
}
