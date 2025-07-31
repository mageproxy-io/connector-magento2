<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Layout;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Config\Generator\Head;
use Mageproxy\Connector\Api\OptimizationManagerInterface;
use Mageproxy\Connector\Model\Config as ModuleConfig;

class PageConfigHeadGeneratorPlugin
{
    private OptimizationManagerInterface $optimizationManager;
    private Config $pageConfig;
    private ScopeConfigInterface $scopeConfig;
    private ModuleConfig $configModel;

    public function __construct(
        OptimizationManagerInterface $optimizationManager,
        Config $pageConfig,
        ScopeConfigInterface $scopeConfig,
        ModuleConfig $configModel
    ) {
        $this->optimizationManager = $optimizationManager;
        $this->pageConfig = $pageConfig;
        $this->scopeConfig = $scopeConfig;
        $this->configModel = $configModel;
    }

    public function afterProcess(
        Head $subject,
        Layout\GeneratorInterface $result,
        Layout\Reader\Context $readerContext,
        Layout\Generator\Context $generatorContext
    ) {

        if (!$this->optimizationManager->getDeployedOptimization()) {
            return $result;
        }

        $optimization = $this->optimizationManager->getDeployedOptimization();

        foreach ($this->filter($optimization->getBundles()) as $bundle) {
            $sriProps['attributes']['integrity'] = $bundle->getSriHash();
            $sriProps['attributes']['crossorigin'] = 'anonymous';
            if ($bundle->getPreload()) {
                $preloadProps['attributes']['rel'] = 'preload';
                $preloadProps['attributes']['as'] = 'script';
                $preloadProps['attributes']['fetchpriority'] = 'high';
                $this->pageConfig->addRemotePageAsset(
                    $bundle->getUrl(),
                    'preload',
                    array_merge_recursive($sriProps, $preloadProps)
                );
            }
            if ($bundle->isCoreBundle()) {
                // Add the core bundle as a synchronous dependency, rest will be loaded async via AMD
                $this->pageConfig->addRemotePageAsset(
                    $bundle->getUrl(),
                    'js',
                    $sriProps
                );
            }
        }
        return $result;
    }

    /**
     * Filter out dependencies that you might want to preload
     * @param array $bundles
     * @return array
     */
    private function filter(array $bundles)
    {
        $bdls = [];
        $moveScriptsToBottom = $this->scopeConfig->isSetFlag('dev/js/move_script_to_bottom');
        foreach ($bundles as $bundle) {
            if ($bundle->isCoreBundle()) {
                $bdls[0] = $bundle;
                if ($moveScriptsToBottom && $this->configModel->getPreloadBundles()) {
                   $bundle->setData('preload', true);
                }
            }
            if (str_contains($bundle->getUrl(), 'bundles/critical')) {
                $bdls[1] = $bundle;
                if ($this->configModel->getPreloadBundles()) {
                    $bundle->setData('preload', true);
                }
            }
        }
        ksort($bdls);
        return $bdls;
    }
}
