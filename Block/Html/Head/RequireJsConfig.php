<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Block\Html\Head;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageproxy\Connector\Api\Data\RequireJsConfigProviderInterface;
use Mageproxy\Connector\Model\RequireJs\ConfigSerializer;

/**
 * In page aggregated RequireJS config provider
 */
class RequireJsConfig extends Template
{
    /**
     * @var \Mageproxy\Connector\Model\RequireJs\ConfigSerializer
     */

    private ConfigSerializer $configSerializer;
    /**
     * @var \Mageproxy\Connector\Api\Data\RequireJsConfigProviderInterface
     */
    private RequireJsConfigProviderInterface $requireJsConfigProvider;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Mageproxy\Connector\Model\RequireJs\ConfigSerializer $configSerializer
     * @param \Mageproxy\Connector\Api\Data\RequireJsConfigProviderInterface $requireJsConfigProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigSerializer $configSerializer,
        RequireJsConfigProviderInterface $requireJsConfigProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configSerializer = $configSerializer;
        $this->requireJsConfigProvider = $requireJsConfigProvider;
    }

    /**
     * @inheritdoc
     */
    protected function _afterToHtml($html)
    {
        /** @var \Magento\Framework\View\Element\Template $configBlock */
        $configBlock = $this->getChildBlock('config');
        if (empty($configBlock)) {
            return $html;
        }
        $mergedConfig = $this->requireJsConfigProvider->getConfig();
        if (empty($mergedConfig)) {
            return $html;
        }
        if (!isset($mergedConfig['config'])) {
            return $html;
        }
        $configBlock->setData('serialized_config', $this->configSerializer->serialize($mergedConfig));
        return $html . $configBlock->toHtml();
    }
}
