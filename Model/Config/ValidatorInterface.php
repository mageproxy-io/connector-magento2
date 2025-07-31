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

interface ValidatorInterface
{

    /**
     * Validate the configuration, if invalid return an array of error messages otherwise empty array.
     *
     * @return array
     */
    public function validate(): array;

    /**
     * @return string
     */
    public function getErrorCode(): string;

    /**
     * If the validation comes back with an error, should the module be disabled
     *
     * @return bool
     */
    public function disableModuleOnError(): bool;
}
