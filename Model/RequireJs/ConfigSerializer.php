<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\RequireJs;

use Magento\Framework\Serialize\Serializer\Json;

class ConfigSerializer
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private Json $serializer;

    public function __construct(
        Json $serializer
    ) {
        $this->serializer = $serializer;
    }

    public function serialize(array $config): string
    {
        return $this->serializer->serialize($config);
    }
}
