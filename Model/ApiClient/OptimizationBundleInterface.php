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

interface OptimizationBundleInterface
{
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
     * @return string
     */
    public function getSriHash(): string;

    /**
     * @param string $sriHash
     * @return void
     */
    public function setSriHash(string $sriHash): void;

    /**
     * @return bool
     */
    public function getMinified(): bool;

    /**
     * @param bool $minified
     * @return void
     */
    public function setMinified(bool $minified): void;

    /**
     * @return int
     */
    public function getRawSize(): int;

    /**
     * @param int $rawSize
     * @return void
     */
    public function setRawSize(int $rawSize): void;

    /**
     * @return int
     */
    public function getComSize(): int;

    /**
     * @param int $comSize
     * @return void
     */
    public function setComSize(int $comSize): void;

    /**
     * @return int
     */
    public function getMinSize(): int;

    /**
     * @param int $minSize
     * @return void
     */
    public function setMinSize(int $minSize): void;
}
