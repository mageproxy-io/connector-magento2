<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\RequireJs\ConfigProvider;

use Mageproxy\Connector\Api\Data\RequireJsConfigProviderInterface;
use Mageproxy\Connector\Helper\Url;

class BaseUrl implements RequireJsConfigProviderInterface
{
    private Url $urlHelper;

    public function __construct(
        Url $urlHelper
    ) {
        $this->urlHelper = $urlHelper;
    }

    /**
     * @inheritdoc
     */
    public function getConfig(): array
    {
        return [
            'baseUrl' => $this->urlHelper->getDefaultStaticBaseUrl(),
        ];
    }
}
