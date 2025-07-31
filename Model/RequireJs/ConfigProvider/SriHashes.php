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

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\LayoutInterface;
use Mageproxy\Connector\Api\Data\RequireJsConfigProviderInterface;
use Mageproxy\Connector\Api\OptimizationManagerInterface;
use Mageproxy\Connector\Helper\Url;

class SriHashes implements RequireJsConfigProviderInterface
{
    private LayoutInterface $layout;
    private SerializerInterface $serializer;
    private OptimizationManagerInterface $optimizationManager;
    private Url $url;

    public function __construct(
        OptimizationManagerInterface $optimizationManager,
        LayoutInterface $layout,
        SerializerInterface $serializer,
        Url $url
    ) {
        $this->layout = $layout;
        $this->serializer = $serializer;
        $this->optimizationManager = $optimizationManager;
        $this->url = $url;
    }

    public function getConfig(): array
    {
        $sriHashes = [];

        /** \Magento\Csp\Block\Sri\Hashes $sriHashesBlock */
        $sriHashesBlock = $this->layout->getBlock('csp.sri.hashes');

        if ($sriHashesBlock) {
            $data = $sriHashesBlock->getSerialized();
            $sriHashes = $this->serializer->unserialize($data);
            $this->layout->unsetElement('csp.sri.hashes');
        }

        if ($optimization = $this->optimizationManager->getDeployedOptimization()) {
            foreach ($optimization->getBundles() as $bundle) {
                if ($bundle->getSriHash() !== null) {
                    $sriHashes[$bundle->getUrl()] = $bundle->getSriHash();
                }
            }
        }

        if (empty($sriHashes)) {
            return [];
        }

        return [
            'config' => [
                self::REQUIREJS_MODULE_CONFIG_KEY => [
                    'sriHashes' => $sriHashes
                ]
            ]
        ];
    }
}
