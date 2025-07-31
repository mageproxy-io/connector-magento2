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

use Magento\Framework\App\CacheInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Mageproxy\Connector\Model\Config;

class JwtTokenProvider
{
    private const ACCESS_TOKEN_CACHE_ID = 'mpx_api_jwt_token';

    private CurlFactory $curlFactory;
    private Config $config;
    private Json $json;
    private CacheInterface $cache;
    private EncryptorInterface $encryptor;

    public function __construct(
        CurlFactory $curlFactory,
        Config $config,
        Json $json,
        CacheInterface $cache,
        EncryptorInterface $encryptor
    ) {
        $this->curlFactory = $curlFactory;
        $this->config = $config;
        $this->json = $json;
        $this->cache = $cache;
        $this->encryptor = $encryptor;
    }

    public function getToken(): string
    {
        $token = $this->cache->load(self::ACCESS_TOKEN_CACHE_ID);
        if ($token) {
            return $this->encryptor->decrypt($token);
        }
        return $this->getNewToken();
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getNewToken(): ?string
    {
        $curl = $this->curlFactory->create();
        $curl->setHeaders([
            'Content-Type' => 'application/json',
        ]);
        $curl->post(
            $this->config->getApiEndpoint(Config::XML_PATH_API_PATH_OAUTH_TOKEN),
            $this->json->serialize([
                'client_id' => $this->config->getClientId(),
                'client_secret' => $this->config->getClientSecret(),
            ])
        );
        if ($curl->getStatus() === 200) {
            $body = $curl->getBody();
            $response = $this->json->unserialize($body);
            $this->saveToken($response['access_token'], $response['expires_in']);
            return $response['access_token'];
        }
        return null;
    }

    private function saveToken(string $token, int $ttl): void
    {
        $this->cache->save($this->encryptor->encrypt($token), self::ACCESS_TOKEN_CACHE_ID, [], $ttl);
    }
}
