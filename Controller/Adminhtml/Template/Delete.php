<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageproxy\Connector\Api\OptimizationTemplateRepositoryInterface;

class Delete extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Mageproxy_Connector::optimization_delete';

    private OptimizationTemplateRepositoryInterface $templateRepository;

    public function __construct(
        Context $context,
        OptimizationTemplateRepositoryInterface $templateRepository
    ) {
        parent::__construct($context);
        $this->templateRepository = $templateRepository;
    }

    public function execute()
    {
        $templateId = (int) $this->getRequest()->getParam('id');
        $redirect = $this->resultRedirectFactory->create()->setPath('mageproxy/recording/');

        if (!$templateId) {
            $this->messageManager->addErrorMessage(__('Template not found.'));
            return $redirect;
        }

        try {
            $this->templateRepository->deleteById($templateId);
            $this->messageManager->addSuccessMessage(__('The template has been deleted.'));
        } catch (CouldNotDeleteException|NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('An error occurred while deleting the template.'));
        }
        return $redirect;
    }
}
