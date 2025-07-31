<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\ApiClient\Auth;

use Mageproxy\Connector\Model\ApiClient\JwtTokenProvider;

class JwtToken implements AuthStrategyInterface
{
    private JwtTokenProvider $jwtTokenProvider;

    public function __construct(
        JwtTokenProvider $jwtTokenProvider
    ) {
        $this->jwtTokenProvider = $jwtTokenProvider;
    }

    public function addHeader(array &$headers): void
    {
        $headers['Authorization'] = 'Bearer ' . $this->jwtTokenProvider->getToken();
    }
}
