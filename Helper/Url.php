<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Helper;

use Magento\Framework\View\Asset\File\FallbackContext;
use Magento\Framework\View\Asset\Repository;
use Mageproxy\Connector\Api\Data\OptimizationInterface;

class Url
{
    /**
     * @var \Magento\Framework\View\Asset\File\FallbackContext|\Magento\Framework\View\Asset\ContextInterface
     */
    private FallbackContext $staticContext;

    /**
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     */
    public function __construct(
        Repository $assetRepo
    ) {
        $this->staticContext = $assetRepo->getStaticViewFileContext();
    }

    /**
     * @return string
     */
    public function getDefaultStaticBaseUrl(): string
    {
        return $this->staticContext->getBaseUrl() . $this->staticContext->getPath();
    }

    /**
     * @param \Mageproxy\Connector\Api\Data\OptimizationInterface $optimization
     * @return string|null
     */
    public function getStaticBaseUrl(OptimizationInterface $optimization): ?string
    {
        $bundles = $optimization->getBundles();
        if (empty($bundles)) {
            return null;
        }
        $bundle = $bundles[0];
        $bundleParts = explode('/', $bundle->getUrl());
        return implode('/', array_slice($bundleParts, 0, -2));
    }
}
