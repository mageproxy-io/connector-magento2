<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Plugin\View\Asset;

use Magento\Framework\RequireJs\Config as RjsConfig;
use Magento\Framework\View\Asset\AssetInterface;
use Magento\Framework\View\Asset\File;
use Magento\Framework\View\Asset\GroupedCollection;
use Magento\Framework\View\Asset\Repository;
use Mageproxy\Connector\Api\OptimizationManagerInterface;
use Mageproxy\Connector\Model\Asset\AssetRegistry;
use Mageproxy\Connector\Model\Asset\PropertyGroupArrayManager;
use Mageproxy\Connector\Model\Asset\RecorderJsAssetFactory;
use Mageproxy\Connector\Model\Config as ModuleConfig;

class GroupedCollectionPlugin
{
    private ModuleConfig $config;
    private bool $processed = false;
    private OptimizationManagerInterface $optimizationManager;
    private AssetRegistry $nonAmd;
    private PropertyGroupArrayManager $propertyGroupArrayManager;
    private Repository $assetRepository;

    public function __construct(
        ModuleConfig $config,
        OptimizationManagerInterface $optimizationManager,
        AssetRegistry $nonAmd,
        PropertyGroupArrayManager $propertyGroupArrayManager,
        Repository $assetRepository
    ) {
        $this->config = $config;
        $this->optimizationManager = $optimizationManager;
        $this->nonAmd = $nonAmd;
        $this->propertyGroupArrayManager = $propertyGroupArrayManager;
        $this->assetRepository = $assetRepository;
    }

    /**
     * This is the only consistent hook we have to manipulate the script assets that render
     * directly in head (including any scripts have been added outside the layout xml files, e.g. requirejs-config.js)
     *
     * @param \Magento\Framework\View\Asset\GroupedCollection $subject
     * @param \Magento\Framework\View\Asset\PropertyGroup[] $propertyGroups
     * @return \Magento\Framework\View\Asset\PropertyGroup[] $result
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetGroups(GroupedCollection $subject, $propertyGroups)
    {
        if ($this->processed) {
            return $propertyGroups;
        }

        $recorderJsAsset = $this->assetRepository
            ->createAsset(RecorderJsAssetFactory::FILE_IDENTIFIER);
        $requireJsAsset = $this->assetRepository
            ->createAsset(RjsConfig::REQUIRE_JS_FILE_NAME);
        $sriJsAsset = $this->assetRepository
            ->createAsset('Magento_Csp::js/sri.js');

        if (!$this->config->getIsEnabled()) {
            // Remove recorder when module disabled
            $this->propertyGroupArrayManager->delete($recorderJsAsset, $propertyGroups);
            return $propertyGroups;
        }

        /*
         * Removing SRI from the assets, which only applies to >2.4.7
         * The actual minimal SRI JS implementation is moved to recorder.js
         * so SRI is remains in place
         */
        $this->propertyGroupArrayManager->delete($sriJsAsset, $propertyGroups);

        if ($this->optimizationManager->deploymentInProgress()) {
            // Remove non AMD assets that would be part of the core bundle
            foreach ($this->nonAmd->registry() as $asset) {
                $this->propertyGroupArrayManager->delete($asset, $propertyGroups);
            }
        } else {
            // Move recorder right after require.js
            // This is really important because
            // a) we want to override the require.onResourceLoad method as early as possible
            // b) we definitely want to run before the requirejs-min-resolver.js file as it
            //    also overrides the require.onResourceLoad method to rewrite .js to .min.js
            //    and we only want to track unminified dependencies
            $this->propertyGroupArrayManager->move(
                $recorderJsAsset, // source
                $requireJsAsset, // after
                $propertyGroups
            );
            return $propertyGroups;
        }

        $this->processed = true;
        return $propertyGroups;
    }

    /**
     * Register non amd assets
     * @see \Magento\Framework\View\Asset\GroupedCollection::getFilteredProperties
     */
    public function afterGetFilteredProperties(
        GroupedCollection $subject,
        $properties,
        AssetInterface $asset
    ) {
        if ($this->config->getIsEnabled()
            && $properties[GroupedCollection::PROPERTY_CONTENT_TYPE] === 'js'
            && $asset instanceof File
        ) {
            $this->nonAmd->register($asset);
        }
        return $properties;
    }
}
