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

use Magento\Framework\FlagManager;

class ConfigValidationFailedFlag
{
    private const FLAG_CONFIG_VALIDATION_FAILED = 'mageproxy_connector_config_validation_failed';

    /**
     * @var \Magento\Framework\FlagManager
     */
    private FlagManager $flagManager;

    /**
     * @param \Magento\Framework\FlagManager $flagManager
     */
    public function __construct(
        FlagManager $flagManager
    ) {
        $this->flagManager = $flagManager;
    }

    /**
     * @return void
     */
    public function set(): void
    {
        $this->flagManager->saveFlag(self::FLAG_CONFIG_VALIDATION_FAILED, true);
    }

    /**
     * @return bool
     */
    public function has(): bool
    {
        return (bool) $this->flagManager->getFlagData(self::FLAG_CONFIG_VALIDATION_FAILED);
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $this->flagManager->deleteFlag(self::FLAG_CONFIG_VALIDATION_FAILED);
    }
}
