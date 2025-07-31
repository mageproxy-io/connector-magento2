<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model;

use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    public const XML_PATH_IS_ENABLED = 'mageproxy_connector/settings/enabled';
    public const XML_PATH_INCLUDE_TIMESTAMP = 'mageproxy_connector/settings/include_timestamp';
    public const XML_PATH_SERVICE_ID = 'mageproxy_connector/settings/service_id';
    public const XML_PATH_CLIENT_ID = 'mageproxy_connector/settings/client_id';
    public const XML_PATH_CLIENT_SECRET = 'mageproxy_connector/settings/client_secret';
    public const XML_PATH_TRACKING_URL = 'mageproxy_connector/settings/tracking_url';
    public const XML_PATH_API_BASE_URL = 'mageproxy_connector/settings/api_base_url';
    public const XML_PATH_API_PATH_GET_RECORDING_DEPS = 'mageproxy_connector/settings/api_path_recording_deps';
    public const XML_PATH_API_PATH_GET_RECORDING_DEPS_CNT_TS = 'mageproxy_connector/settings/api_path_recording_deps_cnt_ts';
    public const XML_PATH_API_PATH_GET_RECORDING_DEPS_CNT = 'mageproxy_connector/settings/api_path_recording_deps_cnt';
    public const XML_PATH_API_PATH_GET_RECORDING_SNAPSHOT = 'mageproxy_connector/settings/api_path_recording_snapshot';
    public const XML_PATH_API_PATH_GET_SERVICE = 'mageproxy_connector/settings/api_path_get_service';
    public const XML_PATH_API_PATH_GET_OPTIMIZATION = 'mageproxy_connector/settings/api_path_get_optimization';
    public const XML_PATH_API_PATH_NEW_RECORDING = 'mageproxy_connector/settings/api_path_new_recording';
    public const XML_PATH_API_PATH_RECORDING_OPTIMIZE = 'mageproxy_connector/settings/api_path_recording_optimize';
    public const XML_PATH_API_PATH_OAUTH_TOKEN = 'mageproxy_connector/settings/api_path_oauth_token';
    public const XML_PATH_API_PATH_POST_OPTIMIZATION_DEPLOY = 'mageproxy_connector/settings/api_path_post_optimization_deploy';
    public const XML_PATH_API_PATH_POST_RECORDING_START = 'mageproxy_connector/settings/api_path_recording_start';
    public const XML_PATH_API_PATH_POST_OPTIMIZATION_REVERT = 'mageproxy_connector/settings/api_path_post_optimization_revert';
    public const XML_PATH_API_KEY = 'mageproxy_connector/settings/api_key';
    public const XML_PATH_RUN_MODE = 'mageproxy_connector/settings/run_mode';
    public const XML_PATH_AUTO_RUN_TYPE = 'mageproxy_connector/settings/auto_run_type';
    public const XML_PATH_AUTO_RUN_DURATION = 'mageproxy_connector/settings/auto_run_duration';
    public const XML_PATH_AUTO_RUN_OPT_FREQ = 'mageproxy_connector/settings/auto_run_opt_freq';
    public const XML_PATH_REQUEST_TIMEOUT = 'mageproxy_connector/settings/request_timeout';
    public const XML_PATH_MINIFY_JS = 'mageproxy_connector/settings/minify_js';
    public const XML_PATH_MINIFY_HTML = 'mageproxy_connector/settings/minify_html';
    public const XML_PATH_RECORD_SCHEDULE = 'mageproxy_connector/settings/record_schedule';
    public const XML_PATH_RECORDING_FLUSH_FPC = 'mageproxy_connector/settings/recording_flush_fpc';
    public const XML_PATH_PRELOAD_BUNDLES = 'mageproxy_connector/settings/preload_bundles';
    public const XML_PATH_PREFETCH_RULES = 'mageproxy_connector/settings/prefetch_rules';

    private ScopeConfigInterface $scopeConfig;
    private MutableScopeConfigInterface $mutableScopeConfig;
    private SerializerInterface $serializer;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        MutableScopeConfigInterface $mutableScopeConfig,
        SerializerInterface $serializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->mutableScopeConfig = $mutableScopeConfig;
        $this->serializer = $serializer;
    }

    /**
     * @return bool
     */
    public function getIsEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_IS_ENABLED);
    }

    /**
     * @return string|null
     */
    public function getServiceId(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SERVICE_ID);
    }

    /**
     * @return string|null
     */
    public function getTrackingUrl(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TRACKING_URL);
    }

    /**
     * @return string|null
     */
    public function getClientId(): ?string
    {
        $value = $this->scopeConfig->getValue(self::XML_PATH_CLIENT_ID);
        if (empty($value)) {
            $value = $this->mutableScopeConfig->getValue(self::XML_PATH_CLIENT_ID);
        }
        return empty($value) ? null : $value;
    }

    /**
     * @return string|null
     */
    public function getClientSecret(): ?string
    {
        $value = $this->scopeConfig->getValue(self::XML_PATH_CLIENT_SECRET);
        if (empty($value)) {
            $value = $this->mutableScopeConfig->getValue(self::XML_PATH_CLIENT_SECRET);
        }
        return empty($value) ? null : $value;
    }

    /**
     * @return string
     */
    public function getApiBaseUrl(): string
    {
        return rtrim((string) $this->scopeConfig->getValue(self::XML_PATH_API_BASE_URL), '/');
    }

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_API_KEY);
    }

    /**
     * @param string $path
     * @param array $params
     * @return string
     */
    public function getApiEndpoint(string $path, array $params = []): string
    {
        $url = $this->getApiBaseUrl();
        $path = $this->scopeConfig->getValue($path);
        $keys = array_map(fn($key) => ':'.$key, array_keys($params));
        $values = array_values($params);
        $path = str_replace($keys, $values, $path);
        return $url . $path;
    }

    /**
     * @return bool
     */
    public function getIncludeTimestamp(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_INCLUDE_TIMESTAMP);
    }

    /**
     * @return string
     */
    public function getRunMode($storeId = null): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_RUN_MODE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @return int
     */
    public function getRequestTimeout()
    {
        return (int) $this->scopeConfig->getValue(self::XML_PATH_REQUEST_TIMEOUT);
    }

    /**
     * @return bool
     */
    public function getMinifyJs(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_MINIFY_JS, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @return bool
     */
    public function getMinifyHtml(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_MINIFY_HTML, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @return array
     */
    public function getRecordSchedule($storeId = null): ?array
    {
        $value = $this->scopeConfig->getValue(self::XML_PATH_RECORD_SCHEDULE, ScopeInterface::SCOPE_STORE, $storeId);
        return is_string($value) ? array_values($this->serializer->unserialize($value)) : null;
    }

    /**
     * @return bool
     */
    public function getRecordingFlushFpc($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_RECORDING_FLUSH_FPC, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return string|null
     */
    public function getAutoRunType($storeId = null): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AUTO_RUN_TYPE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return int|null
     */
    public function getAutoRunDuration($storeId = null): ?int
    {
        $duration = $this->scopeConfig->getValue(self::XML_PATH_AUTO_RUN_DURATION, ScopeInterface::SCOPE_STORE, $storeId);
        return (int) $duration;
    }

    /**
     * @param $storeId
     * @return int|null
     */
    public function getAutoRunOptimizationFrequency($storeId = null): ?int
    {
        return (int) $this->scopeConfig->getValue(self::XML_PATH_AUTO_RUN_OPT_FREQ, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param $storeId
     * @return bool
     */
    public function getPreloadBundles($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_PRELOAD_BUNDLES, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getPrefetchRules($storeId = null): array
    {
        $rulesConf = $this->scopeConfig->getValue(self::XML_PATH_PREFETCH_RULES, ScopeInterface::SCOPE_STORE, $storeId);
        $rulesConf = trim($rulesConf);
        if (!empty($rulesConf)) {
            return array_map(function ($line) {
                $line = trim($line);
                $rule = explode('|', $line);
                if (count($rule) < 2 || count($rule) > 3) {
                    throw new \InvalidArgumentException(sprintf('Invalid prefetch rule: "%s". Expected format: "selector|bundle pattern[|prefetch_on]"', $line));
                }
                return [
                    'selector' => trim($rule[0]),
                    'bundle' => trim($rule[1]),
                    'prefetch_on' => isset($rule[2]) ? trim($rule[2]) : 'interaction',
                ];
            }, preg_split('/\r\n|\n|\r/', $rulesConf, -1, PREG_SPLIT_NO_EMPTY));
        }
        return [];
    }
}
