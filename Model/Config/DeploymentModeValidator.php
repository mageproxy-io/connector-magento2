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
use Magento\Framework\RequireJs\Config;

class DeploymentModeValidator implements ValidatorInterface
{
    private Config $rjsConfig;
    private Filesystem $filesystem;

    const ERROR_CODE = 'DEPLOYMENT_MODE_VALIDATION_ERROR';

    public function __construct(
        Config $rjsConfig,
        Filesystem $filesystem
    ) {
        $this->rjsConfig = $rjsConfig;
        $this->filesystem = $filesystem;
    }

    /**
     * @inheritDoc
     */
    public function validate(): array
    {
        $errors = [];

        $relPath = $this->rjsConfig->getMapFileRelativePath();
        $dir = $this->filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);
        if ($dir->isExist($relPath)) {
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
