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
use Mageproxy\Connector\Api\Data\OptimizationTemplateInterface;
use Mageproxy\Connector\Model\ResourceModel\Optimization\Template as TemplateResource;

class Template extends AbstractModel implements OptimizationTemplateInterface
{
    /** @var string  */
    protected $_eventObject = 'template';
    /** @var string  */
    protected $_eventPrefix = 'mageproxy_optimization_template';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(TemplateResource::class);
    }

    /**
     * @inheritdoc
     */
    public function setMinifyJs(bool $minifyJs): void
    {
        $this->setData(self::MINIFY_JS, $minifyJs);
    }

    /**
     * @inheritdoc
     */
    public function getMinifyJs(): bool
    {
        return (bool) $this->getData(self::MINIFY_JS);
    }

    /**
     * @inheritdoc
     */
    public function setMinifyHtml(bool $minifyHtml): void
    {
        $this->setData(self::MINIFY_HTML, $minifyHtml);
    }

    /**
     * @inheritdoc
     */
    public function getMinifyHtml(): bool
    {
        return (bool) $this->getData(self::MINIFY_HTML);
    }

    /**
     * @inheritdoc
     */
    public function setExcludeDeps(?array $excludeDeps): void
    {
        $this->setData(self::EXCLUDE_DEPS, $excludeDeps);
    }

    /**
     * @inheritdoc
     */
    public function getExcludeDeps(): ?array
    {
        return (array) $this->getData(self::EXCLUDE_DEPS);
    }

    /**
     * @inheritdoc
     */
    public function setHandles(?array $handles): void
    {
        $this->setData(self::HANDLES, $handles);
    }

    /**
     * @inheritdoc
     */
    public function getHandles(): ?array
    {
        return (array) $this->getData(self::HANDLES);
    }

    /**
     * @inheritdoc
     */
    public function setStoreId(int $storeId): void
    {
        $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @inheritdoc
     */
    public function getStoreId(): int
    {
        return (int) $this->getData(self::STORE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setTranspileGlobs(?array $transpileGlobs): void
    {
        $this->setData(self::TRANSPILE_GLOBS, $transpileGlobs);
    }

    /**
     * @inheritdoc
     */
    public function getTranspileGlobs(): ?array
    {
        return $this->getData(self::TRANSPILE_GLOBS);
    }

    /**
     * @inheritdoc
     */
    public function setBrowserslistConfig(?string $browserslistConfig): void
    {
        $this->setData(self::BROWSERSLIST_CONFIG, $browserslistConfig);
    }

    /**
     * @inheritdoc
     */
    public function getBrowserslistConfig(): ?string
    {
        return $this->getData(self::BROWSERSLIST_CONFIG);
    }

    public function setRemoveDeps(?array $removeDeps): void
    {
        $this->setData(self::REMOVE_DEPS, $removeDeps);
    }

    /**
     * @inheritdoc
     */
    public function getRemoveDeps(): ?array
    {
        return $this->getData(self::REMOVE_DEPS);
    }

    /**
     * @inheritdoc
     */
    public function getTemplateId(): ?int
    {
        $id = $this->getData(self::TEMPLATE_ID);
        return $id ? (int) $id : null;
    }

    /**
     * @inheritdoc
     */
    public function setTemplateId(?int $templateId): void
    {
        $this->setData(self::TEMPLATE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setUsePolyfills(bool $usePolyfills): void
    {
        $this->setData(self::USE_POLYFILLS, $usePolyfills);
    }

    /**
     * @inheritdoc
     */
    public function getUsePolyfills(): bool
    {
        return (bool) $this->getData(self::USE_POLYFILLS);
    }

    /**
     * @inheritdoc
     */
    public function getChunkJs(): bool
    {
        return (bool) $this->getData(self::CHUNK_JS);
    }

    /**
     * @inheritdoc
     */
    public function setChunkJs(bool $chunkJs): void
    {
        $this->setData(self::CHUNK_JS, $chunkJs);
    }

    /**
     * @inheritdoc
     */
    public function getChunkJsSize(): int
    {
        $size = $this->getData(self::CHUNK_JS_SIZE);
        return $size === null ? 0 : (int) $size;
    }

    /**
     * @inheritdoc
     */
    public function setChunkJsSize(int $chunkJsSize): void
    {
        $this->setData(self::CHUNK_JS_SIZE, $chunkJsSize);
    }

    public function getIncludeSourceMapJs(): bool
    {
        return (bool) $this->getData(self::INCLUDE_SOURCEMAP_JS);
    }

    public function setIncludeSourceMapJs(bool $includeSourceMapJs): void
    {
        $this->setData(self::INCLUDE_SOURCEMAP_JS, $includeSourceMapJs);
    }
}
