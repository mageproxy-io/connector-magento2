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

class GetServiceResponse implements GetServiceResponseInterface
{
    private string $id;
    private ?string $plan = null;
    private ?string $mode = null;
    private ?array $urls = null;
    private ?string $status;

    public function getPlan(): ?string
    {
        return $this->plan;
    }

    public function setPlan(?string $plan): void
    {
        $this->plan = $plan;
    }

    public function getMode(): ?string
    {
        return $this->mode;
    }

    public function setMode(?string $mode)
    {
        $this->mode = $mode;
    }

    public function getUrls(): ?array
    {
        return $this->urls;
    }

    public function setUrls(?array $urls): void
    {
        $this->urls = $urls;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status)
    {
        $this->status = $status;
    }
}
