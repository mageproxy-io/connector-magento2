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

class OptimizationBundle implements OptimizationBundleInterface
{
    /** @var string  */
    private string $url;

    /** @var string */
    private string $sriHash;

    /** @var bool  */
    private bool $minified;

    /** @var int  */
    private int $rawSize;

    /** @var int */
    private int $minSize;

    /** @var int  */
    private int $comSize;

    /**
     * @inheritdoc
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @inheritdoc
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @inheritdoc
     */
    public function getSriHash(): string
    {
        return $this->sriHash;
    }

    /**
     * @inheritdoc
     */
    public function setSriHash(string $sriHash): void
    {
        $this->sriHash = $sriHash;
    }

    /**
     * @inheritdoc
     */
    public function getMinified(): bool
    {
        return $this->minified;
    }

    /**
     * @inheritdoc
     */
    public function setMinified(bool $minified): void
    {
        $this->minified = $minified;
    }

    public function getRawSize(): int
    {
        return $this->rawSize;
    }

    public function setRawSize($rawSize): void
    {
        $this->rawSize = $rawSize;
    }

    public function getComSize(): int
    {
        return $this->comSize;
    }

    public function setComSize(int $comSize): void
    {
        $this->comSize = $comSize;
    }

    public function getMinSize(): int
    {
        return $this->minSize;
    }

    public function setMinSize(int $minSize): void
    {
        $this->minSize = $minSize;
    }
}
