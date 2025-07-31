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

use Magento\Framework\Api\ExtensibleDataInterface;

interface GetOptimizationResponseInterface extends ExtensibleDataInterface
{
    public const STATUS_REQUESTED = 'requested';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_DEPLOYED = 'deployed';
    public const STATUS_READY = 'ready';
    public const STATUS_ERROR = 'error';

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @param string $status
     * @return void
     */
    public function setStatus(string $status): void;

    /**
     * @return \Mageproxy\Connector\Model\ApiClient\OptimizationBundleInterface[]|null
     */
    public function getBundles(): ?array;

    /**
     * @param \Mageproxy\Connector\Model\ApiClient\OptimizationBundleInterface[] $bundles
     * @return void
     */
    public function setBundles(array $bundles): void;

    /**
     * @return string|null
     */
    public function getError(): ?string;

    /**
     * @param string|null $error
     * @return void
     */
    public function setError(?string $error): void;
}
