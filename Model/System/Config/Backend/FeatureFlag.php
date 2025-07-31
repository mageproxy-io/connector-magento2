<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);
namespace Mageproxy\Connector\Model\System\Config\Backend;

class FeatureFlag extends \Magento\Framework\App\Config\Value
{
    private \Mageproxy\Connector\Model\ConfigValidator $configValidator;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \Mageproxy\Connector\Model\ConfigValidator $configValidator,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->configValidator = $configValidator;
    }

    public function beforeSave()
    {
        if ((bool) $this->getValue() && $this->isValueChanged()) {
            // Feature flag was enabled
            $this->configValidator->validate();
        }
        return parent::beforeSave();
    }
}
