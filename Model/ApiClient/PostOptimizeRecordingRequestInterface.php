<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\ApiClient;

use Magento\Framework\Api\ExtensibleDataInterface;

interface PostOptimizeRecordingRequestInterface extends ExtensibleDataInterface
{
    /**
     * @param bool $minifyHtml
     * @return void
     */
    public function setMinifyHtml(bool $minifyHtml): void;

    /**
     * @return bool
     */
    public function getMinifyHtml(): bool;

    /**
     * @param bool $minifyJs
     * @return void
     */
    public function setMinifyJs(bool $minifyJs): void;

    /**
     * @return bool
     */
    public function getMinifyJs(): bool;

    /**
     * @param array|null $excludeDeps
     * @return void
     */
    public function setExcludeDeps(?array $excludeDeps): void;

    /**
     * @return array
     */
    public function getExcludeDeps(): ?array;

    /**
     * @param array|null $pageHandles
     * @return void
     */
    public function setPageHandles(?array $pageHandles): void;

    /**
     * @return array
     */
    public function getPageHandles(): ?array;

    /**
     * @param \Mageproxy\Connector\Model\ApiClient\DistributionInterface $distribution
     * @return void
     */
    public function setDistribution(DistributionInterface $distribution): void;

    /**
     * @return \Mageproxy\Connector\Model\ApiClient\DistributionInterface
     */
    public function getDistribution(): DistributionInterface;

    /**
     * @param array|null $removeDeps
     * @return void
     */
    public function setRemoveDeps(?array $removeDeps): void;

    /**
     * @return array|null
     */
    public function getRemoveDeps(): ?array;

    /**
     * @return array|null
     */
    public function getTranspileGlobs(): ?array;

    /**
     * @param array|null $transpileGlobs
     * @return void
     */
    public function setTranspileGlobs(?array $transpileGlobs): void;

    /**
     * @param string|null $browserslistConfig
     * @return void
     */
    public function setBrowserslistConfig(?string $browserslistConfig): void;

    /**
     * @return string|null
     */
    public function getBrowserslistConfig(): ?string;

    /**
     * @param bool $usePolyfills
     * @return void
     */
    public function setUsePolyfills(bool $usePolyfills): void;

    /**
     * @return bool
     */
    public function getUsePolyfills(): bool;

    /**
     * @param int|null $chunkSize
     * @return void
     */
    public function setChunkSize(?int $chunkSize): void;

    /**
     * @return int
     */
    public function getChunkSize(): ?int;

    /**
     * @return bool
     */
    public function getIncludeSourcemap(): bool;

    /**
     * @param bool $includeSourcemap
     * @return void
     */
    public function setIncludeSourcemap(bool $includeSourcemap): void;
}
