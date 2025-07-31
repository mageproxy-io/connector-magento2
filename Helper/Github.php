<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Helper;

use Magento\Framework\HTTP\ClientFactory;
use Magento\Framework\Serialize\Serializer\Json;

class Github
{
    private const API_BASE_URL = 'https://api.github.com';
    private const USER_AGENT = 'mageproxy/connector-magento2';

    /**
     * @var \Magento\Framework\HTTP\ClientFactory
     */
    private ClientFactory $clientFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private Json $json;

    /**
     * @param \Magento\Framework\HTTP\ClientFactory $clientFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     */
    public function __construct(
        ClientFactory $clientFactory,
        Json $json
    ) {
        $this->clientFactory = $clientFactory;
        $this->json = $json;
    }

    /**
     * Get the latest release version from the GitHub repository.
     *
     * @return string|null
     */
    public function getLatestRelease(): ?string
    {
        $httpClient = $this->clientFactory->create();
        $httpClient->setHeaders(
            [
                'Content-Type' => 'application/json',
                'User-Agent' => self::USER_AGENT
            ]
        );
        $httpClient->setTimeout(5);

        $repoEndpoint = self::API_BASE_URL . '/repos/mageproxy/connector-magento2/releases/latest';

        try {
            $httpClient->get($repoEndpoint);
        } catch (\Exception $e) {
            return null;
        }

        if ($httpClient->getStatus() !== 200) {
            return null;
        }

        $response = $httpClient->getBody();
        $response = $this->json->unserialize($response);
        return $response['tag_name'] ?? null;
    }
}
