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

class GetOptimizationResponse implements GetOptimizationResponseInterface
{
    private string $status;
    private array $bundles = [];
    private ?string $error = null;

    /**
     * @inheritdoc
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @inheritdoc
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @inheritdoc
     */
    public function getBundles(): array
    {
        return $this->bundles;
    }

    /**
     * @inheritdoc
     */
    public function setBundles(array $bundles): void
    {
        $this->bundles = $bundles;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): void
    {
        $this->error = $error;
    }
}
