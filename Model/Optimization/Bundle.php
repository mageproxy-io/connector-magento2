<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\Optimization;

use Magento\Framework\Model\AbstractModel;
use Mageproxy\Connector\Api\Data\OptimizationBundleInterface;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Model\ResourceModel\Optimization\Bundle as ResourceModel;

class Bundle extends AbstractModel implements OptimizationBundleInterface
{
    private ?OptimizationInterface $optimization = null;

    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getUrl(): string
    {
        return $this->getData(self::URL);
    }

    /**
     * @inheritdoc
     */
    public function setUrl(string $url): void
    {
        $this->setData(self::URL, $url);
    }

    /**
     * @inheritdoc
     */
    public function getSriHash(): ?string
    {
        return $this->getData(self::SRI_HASH);
    }

    /**
     * @inheritdoc
     */
    public function setSriHash(string $sriHash): void
    {
        $this->setData(self::SRI_HASH, $sriHash);
    }

    /**
     * @inheritdoc
     */
    public function setOptimizationId(int $optimizationId): void
    {
        $this->setData(self::OPTIMIZATION_ID, $optimizationId);
    }

    /**
     * @inheritdoc
     */
    public function getOptimizationId(): ?int
    {
        return $this->getData(self::OPTIMIZATION_ID);
    }

    /**
     * @inheritdoc
     */
    public function setOptimization(OptimizationInterface $optimization)
    {
        $this->optimization = $optimization;
    }

    /**
     * @inheritdoc
     */
    public function getOptimization(): ?OptimizationInterface
    {
        return $this->optimization;
    }

    /**
     * @inheritdoc
     */
    public function getRawSize(): ?int
    {
        return (int) $this->getData(self::RAW_SIZE);
    }

    /**
     * @inheritdoc
     */
    public function setRawSize(int $rawSize): void
    {
        $this->setData(self::RAW_SIZE, $rawSize);
    }

    /**
     * @inheritdoc
     */
    public function getMinifiedSize(): ?int
    {
        return (int) $this->getData(self::MINIFIED_SIZE);
    }

    /**
     * @inheritdoc
     */
    public function setMinifiedSize(int $minSize): void
    {
        $this->setData(self::MINIFIED_SIZE, $minSize);
    }

    /**
     * @inheritdoc
     */
    public function getCompressedSize(): ?int
    {
        return (int) $this->getData(self::COMPRESSED_SIZE);
    }

    /**
     * @inheritdoc
     */
    public function setCompressedSize(int $compressedSize): void
    {
        $this->setData(self::COMPRESSED_SIZE, $compressedSize);
    }

    /**
     * @inheritdoc
     */
    public function isCommonBundle(): bool
    {
        return (bool) preg_match('/bundles\/common/', $this->getUrl());
    }

    /**
     * @inheritdoc
     */
    public function isCoreBundle(): bool
    {
        return (bool) preg_match('/bundles\/core/', $this->getUrl());
    }
}
