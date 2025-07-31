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

interface OptimizationTemplateInterface
{
    public const MINIFY_JS = 'minify_js';
    public const MINIFY_HTML = 'minify_html';
    public const EXCLUDE_DEPS = 'exclude_deps';
    public const REMOVE_DEPS = 'remove_deps';
    public const HANDLES = 'handles';
    public const STORE_ID = 'store_id';
    public const BROWSERSLIST_CONFIG = 'browserslist_config';
    public const TRANSPILE_GLOBS = 'transpile_globs';
    public const TEMPLATE_ID = 'template_id';
    public const USE_POLYFILLS = 'use_polyfills';
    public const CHUNK_JS = 'chunk_js';
    public const CHUNK_JS_SIZE = 'chunk_js_size';
    public const INCLUDE_SOURCEMAP_JS = 'include_sourcemap_js';

    /**
     * @param bool $minifyJs
     * @return mixed
     */
    public function setMinifyJs(bool $minifyJs): void;

    /**
     * @return bool
     */
    public function getMinifyJs(): bool;

    /**
     * @param bool $minifyHtml
     * @return mixed
     */
    public function setMinifyHtml(bool $minifyHtml): void;

    /**
     * @return bool
     */
    public function getMinifyHtml(): bool;

    /**
     * @param array|null $excludeDeps
     * @return void
     */
    public function setExcludeDeps(?array $excludeDeps): void;

    /**
     * @return array|null
     */
    public function getExcludeDeps(): ?array;

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
     * @param array|null $handles
     * @return void
     */
    public function setHandles(?array $handles): void;

    /**
     * @return array|null
     */
    public function getHandles(): ?array;

    /**
     * @param int $storeId
     * @return void
     */
    public function setStoreId(int $storeId): void;

    /**
     * @return int
     */
    public function getStoreId(): int;

    /**
     * @param array|null $transpileGlobs
     * @return void
     */
    public function setTranspileGlobs(?array $transpileGlobs): void;

    /**
     * @return array|null
     */
    public function getTranspileGlobs(): ?array;

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
     * @return int|null
     */
    public function getTemplateId(): ?int;

    /**
     * @param int|null $templateId
     * @return void
     */
    public function setTemplateId(?int $templateId): void;

    /**
     * @param bool $usePolyfills
     * @return void
     */
    public function setUsePolyfills(bool $usePolyfills): void;

    /**
     * @return bool
     */
    public function getChunkJs(): bool;

    /**
     * @param bool $chunkJs
     * @return void
     */
    public function setChunkJs(bool $chunkJs): void;

    /**
     * @return int
     */
    public function getChunkJsSize(): int;

    /**
     * @param int $chunkJsSize
     * @return void
     */
    public function setChunkJsSize(int $chunkJsSize): void;

    /**
     * @return bool
     */
    public function getIncludeSourceMapJs(): bool;

    /**
     * @param bool $includeSourceMapJs
     * @return void
     */
    public function setIncludeSourceMapJs(bool $includeSourceMapJs): void;

}
