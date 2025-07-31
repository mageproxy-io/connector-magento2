<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */

// phpcs:ignoreFile
declare(strict_types=1);

namespace Mageproxy\Connector\Model\Asset;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Filesystem;
use Magento\Framework\RequireJs\Config;
use Magento\Framework\View\Asset\Minification;
use Magento\Framework\View\Asset\PreProcessor\MinificationConfigProvider;
use Magento\Framework\View\Asset\Repository;
use Magento\RequireJs\Model\FileManager as CoreFileManager;

/**
 * A service for deploying requirejs configuration files.
 */
class FileManager extends CoreFileManager
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * @var Minification
     */
    private $minification;

    /**
     * @var MinificationConfigProvider
     */
    private $minificationConfig;

    /**
     * If this value is true, we skip minification even if enabled in the configuration.
     *
     * @var bool
     */
    protected $skipMinification = false;

    /**
     * @param Config $config
     * @param Filesystem $appFilesystem
     * @param AppState $appState
     * @param Repository $assetRepo
     */
    public function __construct(
        Config $config,
        Filesystem $appFilesystem,
        AppState $appState,
        Repository $assetRepo,
        Minification $minification,
        MinificationConfigProvider $minificationConfig
    ) {
        $this->config = $config;
        $this->filesystem = $appFilesystem;
        $this->appState = $appState;
        $this->assetRepo = $assetRepo;
        $this->minification = $minification;
        $this->minificationConfig = $minificationConfig;
    }

    /**
     * @inheritdoc
     */
    public function createRequireJsConfigAsset()
    {
        $relPath = $this->config->getConfigFileRelativePath();

        // Handle unminified version, if necessary
        if ($this->skipMinification) {
            $relPathUnminified = str_replace(".min.js", ".js", $relPath);

            $this->config->setForceUnminified(true);
            $this->ensureSourceFile($relPathUnminified);
            $this->config->setForceUnminified(false);

            $file = $this->assetRepo->createArbitrary($relPathUnminified, '');
            $file->setSkipMinification(true);
            return $file;
        }
        
        $this->ensureSourceFile($relPath);
        return $this->assetRepo->createArbitrary($relPath, '');
    }

    /**
     * @inheritdoc
     */
    public function createMinResolverAsset()
    {
        $relPath = $this->config->getMinResolverRelativePath();

        // Handle unminified version, if necessary
        if ($this->skipMinification) {
            $relPathUnminified = str_replace(".min.js", ".js", $relPath);

            $this->config->setForceUnminified(true);
            $this->ensureMinResolverFile($relPathUnminified);
            $this->config->setForceUnminified(false);

            $file = $this->assetRepo->createArbitrary($relPathUnminified, '');
            $file->setSkipMinification(true);
            return $file;
        }

        $this->ensureMinResolverFile($relPath);
        return $this->assetRepo->createArbitrary($relPath, '');
    }

    /**
     * @inheritdoc
     */
    public function createRequireJsMixinsAsset()
    {
        return $this->assetRepo->createArbitrary($this->config->getMixinsFileRelativePath(), '');
    }

    /**
     * @inheritdoc
     */
    public function createRequireJsAsset()
    {
        return $this->assetRepo->createArbitrary($this->config->getRequireJsFileRelativePath(), '');
    }

    /**
     * @inheritdoc
     */
    public function createUrlResolverAsset()
    {
        return $this->assetRepo->createArbitrary($this->config->getUrlResolverFileRelativePath(), '');
    }

    /**
     * @inheritdoc
     */
    public function createRequireJsMapConfigAsset()
    {
        if ($this->checkIfExist($this->config->getMapFileRelativePath())) {
            return $this->assetRepo->createArbitrary($this->config->getMapFileRelativePath(), '');
        } else {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    private function ensureSourceFile($relPath)
    {
        $dir = $this->filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);
        if ($this->appState->getMode() == AppState::MODE_DEVELOPER || !$dir->isExist($relPath)) {
            $dir->writeFile($relPath, $this->config->getConfig());
        }
    }

    /**
     * @inheritdoc
     */
    private function ensureMinResolverFile($relPath)
    {
        $dir = $this->filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);
        if ($this->appState->getMode() == AppState::MODE_DEVELOPER || !$dir->isExist($relPath)) {
            $dir->writeFile($relPath, $this->config->getMinResolverCode());
        }
    }

    /**
     * @inheritdoc
     */
    public function createStaticJsAsset()
    {
        if ($this->appState->getMode() != AppState::MODE_PRODUCTION) {
            return false;
        }
        return $this->assetRepo->createAsset(Config::STATIC_FILE_NAME);
    }

    /**
     * @inheritdoc
     */
    public function createBundleJsPool()
    {
        $bundles = [];
        if ($this->appState->getMode() == AppState::MODE_PRODUCTION) {
            $libDir = $this->filesystem->getDirectoryRead(DirectoryList::STATIC_VIEW);
            /** @var $context \Magento\Framework\View\Asset\File\FallbackContext */
            $context = $this->assetRepo->getStaticViewFileContext();

            $bundleDir = $context->getPath() . '/' . Config::BUNDLE_JS_DIR;

            if (!$libDir->isExist($bundleDir)) {
                return [];
            }

            foreach ($libDir->read($bundleDir) as $bundleFile) {
                if (pathinfo($bundleFile, PATHINFO_EXTENSION) !== 'js') {
                    continue;
                }
                $relPath = $libDir->getRelativePath($bundleFile);
                $bundles[] = $this->assetRepo->createArbitrary($relPath, '');
            }
        }

        return $bundles;
    }

    /**
     * @inheritdoc
     */
    public function clearBundleJsPool()
    {
        $dirWrite = $this->filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);
        /** @var $context \Magento\Framework\View\Asset\File\FallbackContext */
        $context = $this->assetRepo->getStaticViewFileContext();
        $bundleDir = $context->getPath() . '/' . Config::BUNDLE_JS_DIR;
        return $dirWrite->delete($bundleDir);
    }

    /**
     * @inheritdoc
     */
    private function checkIfExist($relPath)
    {
        $dir = $this->filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);
        return $dir->isExist($relPath);
    }

    /**
     * Get the value of skipMinification
     *
     * @return bool
     */
    public function getSkipMinification(): bool
    {
        return $this->skipMinification;
    }

    /**
     * Set the value of skipMinification
     *
     * @param bool $skipMinification
     * @return self
     */
    public function setSkipMinification(bool $skipMinification): self
    {
        $this->skipMinification = $skipMinification;
        return $this;
    }

    /**
     * Check if the config relative path is minified, to avoid calling the function when it's not necessary.
     * 
     * @return bool
     */
    public function isConfigFileRelativePathMinified(): bool
    {
        $relPath = $this->config->getConfigFileRelativePath();
        return $this->minificationConfig->isMinificationEnabled($relPath) &&
            $this->minification->isMinifiedFilename($relPath);
    }

    /**
     * Check if the min resolver relative path is minified, to avoid calling the function when it's not necessary.
     */
    public function isMinResolverRelativePathMinified(): bool
    {
        $relPath = $this->config->getMinResolverRelativePath();
        return $this->minificationConfig->isMinificationEnabled($relPath) &&
            $this->minification->isMinifiedFilename($relPath);
    }
}
