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

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\RequireJs\ConfigFactory;
use Magento\Framework\RequireJs\Config as RequireJsConfig;

class DeploymentModeValidator implements ValidatorInterface
{
    const ERROR_CODE = 'DEPLOYMENT_MODE_VALIDATION_ERROR';

    /**
     * @var \Magento\Framework\Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var \Magento\Framework\RequireJs\ConfigFactory
     */
    private ConfigFactory $rjsConfigFactory;

    /**
     * Optional direct instance for tests/overrides.
     *
     * @var \Magento\Framework\RequireJs\Config|null
     */
    private ?RequireJsConfig $rjsConfig = null;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\RequireJs\ConfigFactory $rjsConfigFactory
     * @param \Magento\Framework\RequireJs\Config|null $rjsConfig
     */
    public function __construct(
        Filesystem $filesystem,
        ConfigFactory $rjsConfigFactory,
        ?RequireJsConfig $rjsConfig = null
    ) {
        $this->filesystem = $filesystem;
        $this->rjsConfigFactory = $rjsConfigFactory;
        $this->rjsConfig = $rjsConfig;
    }

    /**
     * @inheritDoc
     */
    public function validate(): array
    {
        $errors = [];
        /** @var \Magento\Framework\RequireJs\Config $config */
        $config = $this->rjsConfig ?: $this->rjsConfigFactory->create();
        $path = $config->getMapFileRelativePath();
        // Use write interface to satisfy tests and because both interfaces expose isExist()
        $dir = $this->filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);
        if ($dir->isExist($path)) {
            $errors[] = __(
                'You have deployed static assets using compact mode. '
                . 'Please run static content deploy with standard or quick mode'
            );
        }
        return $errors;
    }

    /**
     * @inheritDoc
     */
    public function getErrorCode(): string
    {
        return self::ERROR_CODE;
    }

    /**
     * @inheritDoc
     */
    public function disableModuleOnError(): bool
    {
        return true;
    }
}
