<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\System\Message;

use Magento\Framework\Notification\MessageInterface;
use Mageproxy\Connector\Model\ConfigValidationFailedFlag;

class ConfigVerificationFail implements MessageInterface
{
    private ConfigValidationFailedFlag $flag;

    public function __construct(
        ConfigValidationFailedFlag $flag
    ) {
        $this->flag = $flag;
    }

    /**
     * @inheritDoc
     */
    public function getIdentity()
    {
        return 'mageproxy_connector_config_verification_fail';
    }

    /**
     * @inheritDoc
     */
    public function isDisplayed()
    {
        return $this->flag->has();
    }

    /**
     * @inheritDoc
     */
    public function getText()
    {
        $message = __('Mageproxy Connector configuration validation failed.');
        $message .= __(
            ' Check our <a href="%1">troubleshooting guide</a> for more information.',
            'https://www.mageproxy.io/docs/troubleshooting'
        );
        return $message;
    }

    /**
     * @inheritDoc
     */
    public function getSeverity()
    {
        return MessageInterface::SEVERITY_MAJOR;
    }
}
