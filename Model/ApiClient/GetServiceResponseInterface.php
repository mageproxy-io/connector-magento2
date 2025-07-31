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

interface GetServiceResponseInterface
{
    public const STATUS_ACTIVE = 'active';

    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @param string $id
     * @return void
     */
    public function setId(string $id): void;

    /**
     * @return string|null
     */
    public function getPlan(): ?string;

    /**
     * @param string|null $plan
     * @return void
     */
    public function setPlan(?string $plan): void;

    /**
     * @return string|null
     */
    public function getMode(): ?string;

    /**
     * @param string|null $mode
     * @return mixed
     */
    public function setMode(?string $mode);

    /**
     * @return string|null
     */
    public function getStatus(): ?string;

    /**
     * @param string|null $status
     * @return mixed
     */
    public function setStatus(?string $status);
}
