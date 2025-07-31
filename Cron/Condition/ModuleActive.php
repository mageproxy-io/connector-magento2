<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Cron\Condition;

use Mageproxy\Connector\Cron\RunConditionInterface;
use Mageproxy\Connector\Model\Config;
use Mageproxy\Connector\Model\ConfigValidationFailedFlag;

class ModuleActive implements RunConditionInterface
{
    private Config $config;
    private ConfigValidationFailedFlag $flag;

    public function __construct(
        Config $config,
        ConfigValidationFailedFlag $flag
    ) {
        $this->config = $config;
        $this->flag = $flag;
    }

    public function canRun(): bool
    {
        return $this->config->getIsEnabled() && !$this->flag->has();
    }
}
