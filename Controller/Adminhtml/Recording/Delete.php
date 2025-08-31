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
use Mageproxy\Connector\Api\RecordingManagerInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;

class Delete extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Mageproxy_Connector::recording_delete';

    /**
     * @var \Mageproxy\Connector\Api\RecordingManagerInterface
     */
    private RecordingManagerInterface $recordingManager;

    /**
     * @var \Mageproxy\Connector\Api\RecordingRepositoryInterface
     */
    private RecordingRepositoryInterface $recordingRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Mageproxy\Connector\Api\RecordingManagerInterface $recordingManager
     * @param \Mageproxy\Connector\Api\RecordingRepositoryInterface $recordingRepository
     */
    public function __construct(
        Context $context,
        RecordingManagerInterface $recordingManager,
        RecordingRepositoryInterface $recordingRepository
    ) {
        parent::__construct($context);
        $this->recordingManager = $recordingManager;
        $this->recordingRepository = $recordingRepository;
    }

    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('id');

        try {
            $recording = $this->recordingRepository->getById($id);
            $this->recordingManager->delete($recording);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error occurred while deleting the recording.'));
            return $this->_redirect('*/*/');
        }

        $this->messageManager->addSuccessMessage(__('Recording was deleted successfully.'));
        return $this->_redirect('*/*/');

    }
}
