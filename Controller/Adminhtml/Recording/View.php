<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Controller\Adminhtml\Recording;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Controller\RegistryConstants;

class View extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = "Mageproxy_Connector::proxy";

    private Registry $registry;

    /**
     * @var \Mageproxy\Connector\Api\RecordingRepositoryInterface
     */
    private RecordingRepositoryInterface $recordingRepository;

    public function __construct(
        Context $context,
        Registry $registry,
        RecordingRepositoryInterface $recordingRepository
    ) {
        parent::__construct($context);
        $this->registry = $registry;
        $this->recordingRepository = $recordingRepository;
    }

    public function execute()
    {
        $recordingId = $this->getRequest()->getParam('id');
        if (!empty($recordingId)) {
            try {
                $recording = $this->recordingRepository->getById((int) $recordingId);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('Recording with ID %1 does not exist.', $recordingId));
                return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
            }
            $this->registry->register(RegistryConstants::CURRENT_RECORDING, $recording);
        }

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Mageproxy_Connector::mageproxy');
        $resultPage->getConfig()->getTitle()->prepend(__('Recording'));
        return $resultPage;
    }
}
