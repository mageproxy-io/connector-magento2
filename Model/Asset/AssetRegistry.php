<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\Asset;

use Magento\Framework\View\Asset\File;
use Magento\Framework\View\Asset\Repository;

class AssetRegistry
{
    /**
     * @var array
     */
    private array $matchAssets = [];

    /**
     * @var array
     */
    private array $registry = [];

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private Repository $assetRepo;

    public function __construct(
        Repository $assetRepo,
        $matchAssets = []
    ) {
        $this->assetRepo = $assetRepo;
        foreach ($matchAssets as $moduleId => $assetIdentifier) {
            $this->matchAssets[$moduleId] = $this->assetRepo->createAsset($assetIdentifier);
        }
    }

    /**
     * @return \Magento\Framework\View\Asset\File[]
     */
    public function registry(): array
    {
        return $this->registry;
    }

    /**
     * @param \Magento\Framework\View\Asset\File $asset
     * @return bool
     */
    public function has(File $asset): bool
    {
        return in_array($asset, array_values($this->registry));
    }

    /**
     * @param \Magento\Framework\View\Asset\File $asset
     * @return bool
     */
    public function register(File $asset): bool
    {
        /** @var File $assetToMatch */
        foreach ($this->matchAssets as $moduleId => $assetToMatch) {
            if ($asset->getUrl() === $assetToMatch->getUrl()) {
                $this->registry[$moduleId] = $asset;
                return true;
            }
        }
        return false;
    }
}
