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

use Magento\Framework\View\Asset\AssetInterface;
use Magento\Framework\View\Asset\PropertyGroup;

class PropertyGroupArrayManager
{
    public function move(AssetInterface $asset, AssetInterface $afterAsset, array &$propertyGroups): void
    {
        $assetIdentifier = $afterAssetIdentifier = $moveFromGroupIndex = $moveToGroupIndex = $moveFromGroup = null;
        foreach ($propertyGroups as $index => $group) {
            $identifier = $this->match($group, $asset);
            if ($identifier !== null) {
                $assetIdentifier = $identifier;
                $moveFromGroupIndex = $index;
                $moveFromGroup = $group;
            }
            $identifier = $this->match($group, $afterAsset);
            if ($identifier !== null) {
                $afterAssetIdentifier = $identifier;
                $moveToGroupIndex = $index;
            }
            if ($assetIdentifier !== null && $afterAssetIdentifier !== null) {
                if ($moveFromGroupIndex === $moveToGroupIndex) {
                    $group->remove($assetIdentifier);
                    $group->insert($assetIdentifier, $asset, $afterAssetIdentifier);
                } else {
                    unset($propertyGroups[$moveFromGroupIndex]);
                    array_splice($propertyGroups, $moveToGroupIndex + 1, 0, [$moveFromGroup]);
                }
                break;
            }
        }
    }

    public function delete(AssetInterface $asset, array &$propertyGroups): void
    {
        foreach ($propertyGroups as $index => $group) {
            $identifier = $this->match($group, $asset);
            if ($identifier !== null) {
                $group->remove($identifier);
                if (empty($group->getAll())) {
                    unset($propertyGroups[$index]);
                }
                break;
            }
        }
    }

    private function match(PropertyGroup $group, AssetInterface $matchAsset): ?string
    {
        foreach ($group->getAll() as $identifier => $assetInGroup) {
            if ($assetInGroup->getUrl() === $matchAsset->getUrl()) {
                return $identifier;
            }
        }
        return null;
    }
}
