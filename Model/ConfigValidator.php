<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model;

use Magento\Framework\Exception\LocalizedException;
use Mageproxy\Connector\Model\Config\ValidatorInterface;

class ConfigValidator
{
    private array $validators;

    public function __construct(
        array $validators = []
    ) {
        $this->validators = $validators;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate(): array
    {
        $result = [
            'disabled_module' => false,
            'errors' => []
        ];
        foreach ($this->validators as $validator) {
            if (!$validator instanceof ValidatorInterface) {
                throw new LocalizedException(__('Validator must implement ValidatorInterface'));
            }
            $errors = $validator->validate();
            if ($validator->disableModuleOnError() && !empty($errors)) {
                if (!$result['disabled_module']) {
                    $result['disabled_module'] = true;
                }
            }
            $result['errors'] = array_merge($result['errors'], $errors);
        }
        return $result;
    }
}
