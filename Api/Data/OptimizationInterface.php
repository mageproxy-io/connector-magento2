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

interface OptimizationInterface
{
    public const ID = 'optimization_id';
    public const UUID = 'uuid';
    public const RECORDING_ID = 'recording_id';
    public const STATUS = 'status';
    public const REQUESTED_AT = 'requested_at';
    public const DEPLOYED_AT = 'deployed_at';
    public const REVERTED_AT = 'reverted_at';
    public const MINIFY_JS = 'minify_js';
    public const CHUNK_JS = 'chunk_js';
    public const CHUNK_JS_SIZE = 'chunk_js_size';
    public const MINIFY_HTML = 'minify_html';
    public const STORE_ID = 'store_id';
    public const EXCLUDE_DEPS = 'exclude_deps';
    public const REMOVE_DEPS = 'remove_deps';
    public const HANDLES = 'handles';
    public const DEPS_CNT = 'deps_cnt';
    public const HDLS_CNT = 'hdls_cnt';
    public const REQUESTED_BY = 'requested_by';
    public const BROWSERSLIST_CONFIG = 'browserslist_config';
    public const TRANSPILE_GLOBS = 'transpile_globs';
    public const ERROR_MESSAGE = 'error_message';
    public const REQUESTED_BY_CRON = 'cron';
    public const REQUESTED_BY_USER = 'user';
    public const USE_POLYFILLS = 'use_polyfills';
    public const RECORDING_CHECKSUM = 'recording_checksum';
    public const INCLUDE_SOURCEMAP_JS = 'include_sourcemap_js';

    /*
     * Optimization request send to mageproxy.io
     */
    public const STATUS_REQUESTED = 0;

    /*
     * mageproxy.io published the request
     */
    public const STATUS_PUBLISHED = 1;

    /*
     * Optimization bundle is ready to be deployed
     */
    public const STATUS_READY = 2;

    /*
     * Optimization bundling failed
     */
    public const STATUS_FAILED = 3;

    /*
     * Optimization bundles used in the frontend
     */
    public const STATUS_DEPLOYED = 4;

    /*
     * Terminal status for an optimization
     * Used to indicate an optimization that
     * can no longer be deployed
     */
    public const STATUS_FINISHED = 5;

    /**
     * @return string|null
     */
    public function getUuid(): ?string;

    /**
     * @param string $uuid
     * @return void
     */
    public function setUuid(string $uuid): void;

    /**
     * @param int $recordingId
     * @return void
     */
    public function setRecordingId(int $recordingId): void;

    /**
     * @return int|null
     */
    public function getRecordingId(): ?int;

    /**
     * @return int
     */
    public function getStatus(): int;

    /**
     * @param int $status
     * @return void
     */
    public function setStatus(int $status): void;

    /**
     * @return string
     */
    public function getRequestedAt(): string;

    /**
     * @param string $requestedAt
     * @return void
     */
    public function setRequestedAt(string $requestedAt): void;

    /**
     * @return bool
     */
    public function getMinifyJs(): bool;

    /**
     * @param bool $minifyJs
     * @return void
     */
    public function setMinifyJs(bool $minifyJs): void;

    /**
     * @return bool
     */
    public function getMinifyHtml(): bool;

    /**
     * @param bool $minifyHtml
     * @return void
     */
    public function setMinifyHtml(bool $minifyHtml): void;

    /**
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $recording
     * @return void
     */
    public function setRecording(RecordingInterface $recording): void;

    /**
     * @return int
     */
    public function getStoreId(): int;

    /**
     * @param int $storeId
     * @return void
     */
    public function setStoreId(int $storeId): void;

    /**
     * @return array|null
     */
    public function getExcludeDeps(): ?array;

    /**
     * @param array|null $excludeDeps
     * @return void
     */
    public function setExcludeDeps(?array $excludeDeps): void;

    /**
     * @return array|null
     */
    public function getHandles(): ?array;

    /**
     * @param array|null $handles
     * @return void
     */
    public function setHandles(?array $handles): void;

    /**
     * @return int
     */
    public function getDepsCount(): int;

    /**
     * @param int $depsCount
     * @return void
     */
    public function setDepsCount(int $depsCount): void;

    /**
     * @return string|null
     */
    public function getDeployedAt(): ?string;

    /**
     * @param string|null $deployedAt
     * @return void
     */
    public function setDeployedAt(?string $deployedAt): void;

    /**
     * @return string
     */
    public function getRequestedBy(): string;

    /**
     * @param string $requestedBy
     * @return void
     */
    public function setRequestedBy(string $requestedBy): void;

    /**
     * @return array|null
     */
    public function getRemoveDeps(): ?array;

    /**
     * @param array|null $removeDeps
     * @return void
     */
    public function setRemoveDeps(?array $removeDeps): void;

    /**
     * @param null|string $browserslistConfig
     * @return void
     */
    public function setBrowserslistConfig(?string $browserslistConfig): void;

    /**
     * @return null|array
     */
    public function getBrowserslistConfig(): ?string;

    /**
     * @param null|array $transpileGlobs
     * @return void
     */
    public function setTranspileGlobs(?array $transpileGlobs): void;

    /**
     * @return null|array
     */
    public function getTranspileGlobs(): ?array;

    /**
     * @param string|null $errorMessage
     * @return void
     */
    public function setErrorMessage(?string $errorMessage): void;

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string;

    /**
     * @return int
     */
    public function getHdlsCount(): int;

    /**
     * @param int $hdlsCnt
     * @return void
     */
    public function setHdlsCount(int $hdlsCnt): void;

    /**
     * @return bool
     */
    public function getUsePolyfills(): bool;

    /**
     * @param bool $usePolyfills
     * @return void
     */
    public function setUsePolyfills(bool $usePolyfills): void;

    /**
     * @return string
     */
    public function getRecordingChecksum(): ?string;

    /**
     * @param string $snapshot
     * @return void
     */
    public function setRecordingChecksum(?string $snapshot): void;

    /**
     * @return string|null
     */
    public function getRevertedAt(): ?string;

    /**
     * @param string $revertedAt
     * @return void
     */
    public function setRevertedAt(string $revertedAt): void;

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
