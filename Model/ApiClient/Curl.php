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

use Magento\Framework\HTTP\Client\Curl as CurlLibrary;

/**
 * Add DELETE method
 */
class Curl extends CurlLibrary
{
    public function delete($uri)
    {
        $this->makeRequest('DELETE', $uri);
    }
}
