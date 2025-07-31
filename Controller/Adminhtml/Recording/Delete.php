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
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;

class Delete extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Mageproxy_Connector::recording_delete';

    private RecordingRepositoryInterface $recordingRepository;

    public function __construct(
        Context $context,
        RecordingRepositoryInterface $recordingRepository
    ) {
        parent::__construct($context);
        $this->recordingRepository = $recordingRepository;
    }

    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('id');

        try {
            $this->recordingRepository->deleteById($id);
        } catch (CouldNotDeleteException|NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('An error occurred while deleting the recording.'));
            return $this->_redirect('*/*/');
        }

        $this->messageManager->addSuccessMessage(__('Recording was deleted successfully.'));
        return $this->_redirect('*/*/');

    }
}
