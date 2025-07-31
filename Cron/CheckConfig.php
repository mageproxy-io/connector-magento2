<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Cron;

use Mageproxy\Connector\Model\ConfigValidationFailedFlag;
use Mageproxy\Connector\Model\ConfigValidator;

class CheckConfig
{
    private ConfigValidator $configValidator;
    private ConfigValidationFailedFlag $flag;

    public function __construct(
        ConfigValidator $configValidator,
        ConfigValidationFailedFlag $flag
    ) {
        $this->configValidator = $configValidator;
        $this->flag = $flag;
    }

    public function execute(): void
    {
        $result = $this->configValidator->validate();
        if (empty($result['errors'])) {
            $this->flag->clear();
        } else {
            $this->flag->set();
        }
    }
}
