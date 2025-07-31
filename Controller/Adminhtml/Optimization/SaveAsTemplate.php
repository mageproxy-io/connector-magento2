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
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Mageproxy\Connector\Api\Data\OptimizationTemplateInterface;
use Mageproxy\Connector\Api\Data\OptimizationTemplateInterfaceFactory;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Api\OptimizationTemplateRepositoryInterface;

class SaveAsTemplate extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Mageproxy_Connector::optimization_create';

    private OptimizationRepositoryInterface $optimizationRepository;
    private OptimizationTemplateInterfaceFactory $optimizationTemplateInterfaceFactory;
    private OptimizationTemplateRepositoryInterface $optimizationTemplateRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    public function __construct(
        Context $context,
        OptimizationRepositoryInterface $optimizationRepository,
        OptimizationTemplateInterfaceFactory $optimizationTemplateInterfaceFactory,
        OptimizationTemplateRepositoryInterface $optimizationTemplateRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct($context);
        $this->optimizationRepository = $optimizationRepository;
        $this->optimizationTemplateInterfaceFactory = $optimizationTemplateInterfaceFactory;
        $this->optimizationTemplateRepository = $optimizationTemplateRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function execute()
    {
        $jsonResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $id = (int) $this->getRequest()->getParam('optimization_id');
        $storeChoice = (int) $this->getRequest()->getParam('store');

        $error = false;
        $message = __('Template was saved successfully.');

        try {
            $optimization = $this->optimizationRepository->getById($id);

            $storeId = $storeChoice ? $optimization->getStoreId() : 0;

            $template = $this->getTemplateForStore($storeId);

            $template->setMinifyJs($optimization->getMinifyJs());
            $template->setMinifyHtml($optimization->getMinifyHtml());
            $template->setExcludeDeps($optimization->getExcludeDeps());
            $template->setRemoveDeps($optimization->getRemoveDeps());
            $template->setHandles($optimization->getHandles());
            $template->setStoreId($storeId);
            $template->setBrowserslistConfig($optimization->getBrowserslistConfig());
            $template->setTranspileGlobs($optimization->getTranspileGlobs());
            $template->setUsePolyfills($optimization->getUsePolyfills());
            $template->setChunkJs($optimization->getChunkJs());
            $template->setChunkJsSize($optimization->getChunkJsSize());
            $template->setIncludeJsSourceMap($optimization->getIncludeJsSourceMap());
            $this->optimizationTemplateRepository->save($template);

        } catch (\Exception $e) {
            $message = __('An error occurred while trying to save the template.');
            $error = true;
        }

        return $jsonResult->setData([
            'error' => $error,
            'message' => $message
        ]);
    }

    private function getTemplateForStore(int $storeId): OptimizationTemplateInterface
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('store_id', $storeId)->create();
        $result = $this->optimizationTemplateRepository->getList($searchCriteria);
        if ($result->getTotalCount() > 0) {
            $items = $result->getItems();
            return array_shift($items);
        }
        return $this->optimizationTemplateInterfaceFactory->create();
    }
}
