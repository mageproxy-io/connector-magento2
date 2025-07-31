<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Api\Data;

/**
 * Represents an individual bundle of an optimization
 */
interface OptimizationBundleInterface
{
    const URL = 'url';
    const SRI_HASH = 'sri_hash';
    const OPTIMIZATION_ID = 'optimization_id';
    const RAW_SIZE = 'raw_size';
    const MINIFIED_SIZE = 'min_size';
    const COMPRESSED_SIZE = 'com_size';

    /**
     * @return string
     */
    public function getUrl(): string;

    /**
     * @param string $url
     * @return void
     */
    public function setUrl(string $url): void;

    /**
     * @return string|null
     */
    public function getSriHash(): ?string;

    /**
     * @param string $sriHash
     * @return void
     */
    public function setSriHash(string $sriHash): void;

    /**
     * @param int $optimizationId
     * @return void
     */
    public function setOptimizationId(int $optimizationId): void;

    /**
     * @return \Mageproxy\Connector\Api\Data\OptimizationInterface|null
     */
    public function getOptimization(): ?OptimizationInterface;

    /**
     * @return int|null
     */
    public function getOptimizationId(): ?int;

    /**
     * @return int|null
     */
    public function getRawSize(): ?int;

    /**
     * @param int $rawSize
     * @return void
     */
    public function setRawSize(int $rawSize): void;

    /**
     * @return int|null
     */
    public function getMinifiedSize(): ?int;

    /**
     * @param int $minSize
     * @return void
     */
    public function setMinifiedSize(int $minSize): void;

    /**
     * @return int|null
     */
    public function getCompressedSize(): ?int;

    /**
     * @param int $compressedSize
     * @return void
     */
    public function setCompressedSize(int $compressedSize): void;

    /**
     * @return bool
     */
    public function isCommonBundle(): bool;

    /**
     * @return bool
     */
    public function isCoreBundle(): bool;
}
