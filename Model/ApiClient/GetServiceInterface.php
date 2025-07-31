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

interface GetServiceInterface
{
    const STATUS_ACTIVE = 'active';

    /**
     * @param string $id
     * @return \Mageproxy\Connector\Model\ApiClient\GetServiceResponseInterface|null
     * @throws \Magento\Framework\Exception\AuthenticationException
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute(string $id): ?GetServiceResponseInterface;
}
