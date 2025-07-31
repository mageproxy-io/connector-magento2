<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\Config;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\NotFoundException;
use Mageproxy\Connector\Model\ApiClient\GetServiceInterface;
use Mageproxy\Connector\Model\ApiClient\GetServiceResponseInterface;
use Mageproxy\Connector\Model\Config;

class ServiceIdValidator implements ValidatorInterface
{
    private Config $config;
    private GetServiceInterface $getServiceApiClient;

    public function __construct(
        Config $config,
        GetServiceInterface $getServiceApiClient
    ) {
        $this->config = $config;
        $this->getServiceApiClient = $getServiceApiClient;
    }

    /**
     * @inheritDoc
     */
    public function validate(): array
    {
        $errors = [];

        $serviceId = $this->config->getServiceId();

        if (!$serviceId) {
            return $errors;
        }

        try {
            $result = $this->getServiceApiClient->execute($serviceId);
            if (!$result) {
                $errors[] = __('An error occurred while validating the service ID. Please contact support.');
                return $errors;
            }
            if ($result->getStatus() !== GetServiceResponseInterface::STATUS_ACTIVE) {
                $errors[] = __('The service ID is not active. Please contact support.');
            }
        } catch (AuthenticationException $e) {
            $errors[] = $e->getMessage();
        } catch (NotFoundException $e) {
            $errors[] = __('The service ID is invalid. Please contact support');
        } catch (\Exception $e) {
            $errors[] = __('An error occurred while validating the service ID. Please contact support.');
        }

        return $errors;
    }

    public function getErrorCode(): string
    {
        return '';
    }

    public function disableModuleOnError(): bool
    {
        return false;
    }
}
