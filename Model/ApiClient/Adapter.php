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

use BadMethodCallException;
use Exception;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Mageproxy\Connector\Model\ApiClient\Auth\AuthStrategyInterface;
use Mageproxy\Connector\Model\ApiClient\Exception\ApiException;
use Mageproxy\Connector\Model\Config;

/**
 * @method post(?$requestObject = null, array $params = []): mixed|null
 * @method get(array $params = []): mixed|null
 */
class Adapter
{

    private CurlFactory $curlFactory;
    private Json $json;
    private Config $config;
    private DataObjectHelper $dataObjectHelper;
    private ?string $endpointConfigPath;
    private ?ResponseFactory $responseFactory;
    private ExtensibleDataObjectConverter $extensibleDataObjectConverter;
    private AuthStrategyInterface $authStrategy;

    public function __construct(
        DataObjectHelper $dataObjectHelper,
        CurlFactory $curlFactory,
        Config $config,
        Json $json,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        AuthStrategyInterface $authStrategy,
        string $endpointConfigPath = null,
        ResponseFactory $responseFactory = null
    ) {
        $this->curlFactory = $curlFactory;
        $this->json = $json;
        $this->config = $config;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->endpointConfigPath = $endpointConfigPath;
        $this->responseFactory = $responseFactory;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->authStrategy = $authStrategy;
    }

    public function __call($method, $arguments)
    {
        $curl = $this->curlFactory->create();
        if (!method_exists($curl, $method)) {
            throw new BadMethodCallException("Method $method not found");
        }
        $method = strtolower($method);
        $headers['Content-Type'] = 'application/json';
        $this->authStrategy->addHeader($headers);
        $curl->setHeaders($headers);
        $curl->setTimeout($this->config->getRequestTimeout());
        if ($method === 'post') {
            $requestObject = array_shift($arguments);
            if (!empty($requestObject)) {
                $payload = $this->extensibleDataObjectConverter->toNestedArray(
                    $requestObject,
                    [],
                    $this->getInterface($requestObject)
                );
            }
        }
        $params = array_shift($arguments) ?? [];
        $endpoint = $this->config->getApiEndpoint($this->endpointConfigPath, $params);
        $payload = $payload ?? [];
        if (!in_array($method, ['get', 'post'])) {
            throw new BadMethodCallException(
                __('Method %1 is not supported in the API adapter. Use "GET" or "POST".', $method)
            );
        }
        try {
            if ($method === 'post') {
                $curl->post($endpoint, $this->json->serialize($payload));
            } elseif ($method === 'get') {
                $curl->get($endpoint);
            }
        } catch (Exception $e) {
            throw new ApiException(
                __('An error occurred while making the API request: %1', $e->getMessage())
            );
        }
        switch($curl->getStatus()) {
            case 200:
                break;
            case 401:
            case 403;
                throw new AuthenticationException(
                    __('Unauthorized. Check the Service ID and Api Key settings in configuration.')
                );
            case 404:
                throw new NotFoundException(__('Resource not found'));
            case 400:
            case 500:
            case 503;
                throw new LocalizedException(__('An error occurred. Please contact support.'));
            default:
                return null;
        }
        $body = $curl->getBody();
        if (!$body) {
            return null;
        }
        $responseData = $this->json->unserialize($body);
        return $this->buildResponse($responseData);
    }

    private function buildResponse(array $responseData)
    {
        if (!$this->responseFactory) {
            return $responseData;
        }
        $response = $this->responseFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $response,
            $responseData,
            $this->getInterface($response)
        );
        return $response;
    }

    private function getInterface($object)
    {
        $interfaces = class_implements($object);
        $result = array_filter(
            $interfaces,
            function ($interface) {
                return strpos($interface, 'Mageproxy\\Connector\\Model\\ApiClient\\') !== false;
            }
        );
        return array_shift($result);
    }
}
