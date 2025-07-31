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
use Magento\Framework\Exception\LocalizedException;
use Mageproxy\Connector\Api\RecordingManagerInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Model\PurgeFullPageCache;

class Start extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Mageproxy_Connector::recording_start';

    private RecordingRepositoryInterface $recordingRepository;
    private RecordingManagerInterface $recordingManager;
    private PurgeFullPageCache $purgeFullPageCache;

    public function __construct(
        RecordingRepositoryInterface $recordingRepository,
        RecordingManagerInterface $recordingManager,
        PurgeFullPageCache $purgeFullPageCache,
        Context $context
    ) {
        parent::__construct($context);
        $this->recordingRepository = $recordingRepository;
        $this->recordingManager = $recordingManager;
        $this->purgeFullPageCache = $purgeFullPageCache;
    }

    public function execute()
    {
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $recordingId = (int) $this->getRequest()->getParam('id');
        if (empty($recordingId)) {
            $this->messageManager->addErrorMessage(__('The recording ID is missing.'));
            $redirect->setPath('*/*/');
            return $redirect;
        }
        $redirect->setPath('*/*/view/', [
            'id' => $recordingId
        ]);
        try {
            $recording = $this->recordingRepository->getById($recordingId);
            $this->recordingManager->start($recording);
            $this->purgeFullPageCache->execute();
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $redirect;
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('An error occurred while starting the recording.'));
            return $redirect;
        }

        $this->messageManager->addSuccessMessage(__('The recording was started successfully.'));
        return $redirect;
    }
}
