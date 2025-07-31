<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Controller\Adminhtml\Optimization;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\Data\OptimizationTemplateInterfaceFactory;
use Mageproxy\Connector\Api\OptimizationManagerInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Mageproxy_Connector::optimization_create';

    private RecordingRepositoryInterface $recordingRepository;
    private OptimizationManagerInterface $optimizationManager;
    private OptimizationTemplateInterfaceFactory $optimizationTemplateFactory;

    public function __construct(
        Context $context,
        RecordingRepositoryInterface $recordingRepository,
        OptimizationManagerInterface $optimizationManager,
        OptimizationTemplateInterfaceFactory $optimizationTemplateFactory
    ) {
        parent::__construct($context);
        $this->recordingRepository = $recordingRepository;
        $this->optimizationManager = $optimizationManager;
        $this->optimizationTemplateFactory = $optimizationTemplateFactory;
    }

    public function execute()
    {

        $recordingId = (int) $this->getRequest()->getParam('recording_id');

        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath('*/recording/view', ['id' => $recordingId]);

        try {
            $recording = $this->recordingRepository->getById($recordingId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(
                __('Unable to retrieve recording for optimization')
            );
            return $redirect;
        }

        $minifyJs = (bool) $this->getRequest()->getParam('minify_js');
        $minifyHtml = (bool) $this->getRequest()->getParam('minify_html');

        $excludeDeps = array_map(function ($depInfo) {
            return $depInfo['module_id'];
        }, $this->getRequest()->getParam('assigned_deps_exclude', []));

        $removeDeps = array_map(function ($depInfo) {
            return $depInfo['module_id'];
        }, $this->getRequest()->getParam('assigned_deps_remove', []));

        $hdls = $this->getRequest()->getParam('assigned_hdls', []);
        usort($hdls, function ($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });
        $hdls = array_map(function ($hdlInfo) {
            return $hdlInfo['page_handle'];
        }, $hdls);

        // transpile globs
        $transpileGlobs = array_map(function ($path) {
            return $path['glob'];
        }, $this->getRequest()->getParam('transpile_globs', []));

        // browserslist config
        $browserslistConfig = $this->getRequest()->getParam('browserslist_config', '');
        $browserslistConfig = str_replace(["\r\n", "\r", "\n"], ', ', $browserslistConfig); //default to OR combo
        if (empty($browserslistConfig) && !empty($transpileGlobs)) {
            $browserslistConfig = 'defaults';
        }

        // Polyfill
        $usePolyfills = (boolean) $this->getRequest()->getParam('use_polyfills', false);

        // Chunking
        $chunkJs = (bool) $this->getRequest()->getParam('chunk_js', true);
        $chunkJsSize = $chunkJs
            ? (int) $this->getRequest()->getParam('chunk_js_size')
            : 0;

        // Source map
        $includeSourceMapJs = (bool) $this->getRequest()->getParam('include_sourcemap_js', false);

        try {
            $this->optimizationManager->request(
                $recording,
                OptimizationInterface::REQUESTED_BY_USER,
                $this->optimizationTemplateFactory->create(['data' => [
                    'minify_js' => $minifyJs,
                    'minify_html' => $minifyHtml,
                    'exclude_deps' => $excludeDeps,
                    'remove_deps' => $removeDeps,
                    'handles' => $hdls,
                    'transpile_globs' => $transpileGlobs,
                    'browserslist_config' => $browserslistConfig,
                    'use_polyfills' => $usePolyfills,
                    'chunk_js' => $chunkJs,
                    'chunk_js_size' => $chunkJsSize,
                    'include_sourcemap_js' => $includeSourceMapJs
                ]])
            );
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e);
            return $redirect;
        }

        $this->messageManager->addSuccessMessage(
            __('Successfully requested recording optimization. ' .
                'Check the latest status in the "Optimizations" section.')
        );

        return $redirect;
    }
}
