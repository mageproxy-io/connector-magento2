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
use Magento\Framework\App\View\Asset\MaterializationStrategy;
use Magento\Framework\App\View\Asset\Publisher as CorePublisher;
use Magento\Framework\Filesystem\Directory\WriteFactory;
use Magento\Framework\View\Asset\LocalInterface;
use Magento\Framework\View\Asset\Minification;
use Magento\Framework\View\Asset\PreProcessor\MinificationConfigProvider;

/**
 * Overriding this class so we can make sure the original JS files are also materialized
 * when native minification is enabled
 */
class Publisher extends CorePublisher
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var MaterializationStrategy\Factory
     */
    private $materializationStrategyFactory;

    /**
     * @var WriteFactory
     */
    private $writeFactory;

    /**
     * @var Minification
     */
    private $minification;

    /**
     * @var MinificationConfigProvider
     */
    private $minificationConfig;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param MaterializationStrategy\Factory $materializationStrategyFactory
     * @param WriteFactory $writeFactory
     * @param Minification $minification
     * @param MinificationConfigProvider $minificationConfig
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        MaterializationStrategy\Factory $materializationStrategyFactory,
        WriteFactory $writeFactory,
        Minification $minification,
        MinificationConfigProvider $minificationConfig
    ) {
        $this->filesystem = $filesystem;
        $this->materializationStrategyFactory = $materializationStrategyFactory;
        $this->writeFactory = $writeFactory;
        $this->minification = $minification;
        $this->minificationConfig = $minificationConfig;
    }

    /**
     * @param LocalInterface $asset
     * @return bool
     */
    public function publish(LocalInterface $asset)
    {
        $dir = $this->filesystem->getDirectoryRead(DirectoryList::STATIC_VIEW);
        if ($dir->isExist($asset->getPath())) {
            return true;
        }

        return $this->publishAsset($asset);
    }

    /**
     * Publish the asset
     *
     * @param LocalInterface $asset
     * @return bool
     */
    private function publishAsset(LocalInterface $asset)
    {
        // Generate duplicate JS with no minification applied
        // If an only IF
        //  1. Minification is enabled in the configuration
        //  2. Destination is a minified file
        //  3. Source if NOT a minified file
        if (($asset instanceof FileEx) && $asset->getContentType() === 'js') {
            $targetPath = $asset->getPath();
            if ($this->minificationConfig->isMinificationEnabled($targetPath) &&
                $this->minification->isMinifiedFilename($targetPath) &&
                !$this->minification->isMinifiedFilename($asset->getRelativeSourceFilePath())
            ) {
                // Clone asset as we need to run processing twice.
                $duplicateAsset = clone $asset;
                $duplicateAsset->setSkipMinification(true);
                if (!$this->executePublishAsset($duplicateAsset)) {
                    return false;
                }
            }
        }

        // Run original publish
        return $this->executePublishAsset($asset);
    }

    /**
     * Publish asset
     *
     * @param LocalInterface $asset
     * @return bool
     */
    private function executePublishAsset(LocalInterface $asset)
    {
        $targetDir = $this->filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);
        $fullSource = $asset->getSourceFile();
        $source = basename($fullSource);
        $sourceDir = $this->writeFactory->create(dirname($fullSource));
        $destination = $asset->getPath();
        $strategy = $this->materializationStrategyFactory->create($asset);
        return $strategy->publishFile($sourceDir, $targetDir, $source, $destination);
    }
}
