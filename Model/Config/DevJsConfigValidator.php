<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\Config;

use Magento\Framework\View\Asset\ConfigInterface;

class DevJsConfigValidator implements ValidatorInterface
{
    public const ERROR_CODE = 'DEV_JS_CONFIG_VALIDATION_ERROR';

    private ConfigInterface $assetConfig;

    public function __construct(
        ConfigInterface $assetConfig
    ) {
        $this->assetConfig = $assetConfig;
    }

    /**
     * @inheritDoc
     */
    public function validate(): array
    {
        $errors = [];

        if ($this->assetConfig->isMergeJsFiles()) {
            $errors[] = __(
                'Merge js files is enabled. '
                . 'Please disable this setting in system configuration.'
            );
        }
        if ($this->assetConfig->isBundlingJsFiles()) {
            $errors[] = __(
                'JavaScript bundling is enabled in system configuration. '
                . 'To allow this module to work correctly, this setting must be disabled.'
            );
        }

        return $errors;
    }

    /**
     * @inheritdoc
     */
    public function getErrorCode(): string
    {
        return self::ERROR_CODE;
    }

    /**
     * @inheritdoc
     */
    public function disableModuleOnError(): bool
    {
        return true;
    }
}
