<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */

declare(strict_types=1);

namespace Mageproxy\Connector\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Theme\Model\Url\Plugin\Signature;

class StaticSignValidator implements ValidatorInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function validate(): array
    {
        $errors = [];
        if (!$this->scopeConfig->isSetFlag(Signature::XML_PATH_STATIC_FILE_SIGNATURE)) {
            $errors[] = 'Static file signing is disabled.';
        }
        return $errors;
    }

    public function getErrorCode(): string
    {
        return 'static_sign_disabled';
    }

    public function disableModuleOnError(): bool
    {
        return false;
    }
}
