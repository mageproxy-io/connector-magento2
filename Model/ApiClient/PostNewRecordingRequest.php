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

use Magento\Framework\DataObject;

class PostNewRecordingRequest extends DataObject implements PostNewRecordingRequestInterface
{
    public function setServiceId(string $serviceId): void
    {
        $this->setData('service_id', $serviceId);
    }

    public function getServiceId(): string
    {
        return $this->getData('service_id');
    }

    public function getDomain(): string
    {
        return $this->getData('domain');
    }

    public function setDomain(string $domain): void
    {
        $this->setData('domain', $domain);
    }
}
