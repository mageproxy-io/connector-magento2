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

class PostOptimizeRecordingRequest implements PostOptimizeRecordingRequestInterface
{
    private bool $minifyHtml = false;
    private bool $minifyJs = false;
    private bool $usePolyfills = false;
    private bool $includeSourcemap = false;
    private ?array $excludeDeps = [];
    private ?array $pageHandles = [];
    private ?array $removeDeps = [];
    private ?array $transpileGlobs = [];
    private ?string $browserslistConfig = null;
    private ?int $chunkSize = null;
    private DistributionInterface $distribution;

    public function setMinifyHtml(bool $minifyHtml): void
    {
        $this->minifyHtml = $minifyHtml;
    }

    public function getMinifyHtml(): bool
    {
        return $this->minifyHtml;
    }

    public function setMinifyJs(bool $minifyJs): void
    {
        $this->minifyJs = $minifyJs;
    }

    public function getMinifyJs(): bool
    {
        return $this->minifyJs;
    }

    public function setExcludeDeps(?array $excludeDeps): void
    {
        $this->excludeDeps = $excludeDeps;
    }

    public function getExcludeDeps(): ?array
    {
        return $this->excludeDeps;
    }

    public function setDistribution(DistributionInterface $distribution): void
    {
        $this->distribution = $distribution;
    }

    public function getDistribution(): DistributionInterface
    {
        return $this->distribution;
    }

    public function setPageHandles(?array $pageHandles): void
    {
        $this->pageHandles = $pageHandles;
    }

    public function getPageHandles(): ?array
    {
        return $this->pageHandles;
    }

    public function setRemoveDeps(?array $removeDeps): void
    {
        $this->removeDeps = $removeDeps;
    }

    public function getRemoveDeps(): ?array
    {
        return $this->removeDeps;
    }

    public function getTranspileGlobs(): ?array
    {
        return $this->transpileGlobs;
    }

    public function setTranspileGlobs(?array $transpileGlobs): void
    {
        $this->transpileGlobs = $transpileGlobs;
    }

    public function setBrowserslistConfig(?string $browserslistConfig): void
    {
        $this->browserslistConfig = $browserslistConfig;
    }

    public function getBrowserslistConfig(): ?string
    {
        return $this->browserslistConfig;
    }

    public function setUsePolyfills(bool $usePolyfills): void
    {
        $this->usePolyfills = $usePolyfills;
    }

    public function getUsePolyfills(): bool
    {
        return $this->usePolyfills;
    }

    public function setChunkSize(?int $chunkSize): void
    {
        $this->chunkSize = $chunkSize;
    }

    public function getChunkSize(): ?int
    {
        return $this->chunkSize;
    }

    public function getIncludeSourcemap(): bool
    {
        return $this->includeSourcemap;
    }

    public function setIncludeSourcemap(bool $includeSourcemap): void
    {
        $this->includeSourcemap = $includeSourcemap;
    }
}
