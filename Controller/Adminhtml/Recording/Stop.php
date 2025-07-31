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

class Stop extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Mageproxy_Connector::recording_stop';

    private RecordingRepositoryInterface $recordingRepository;
    private RecordingManagerInterface $recordingManager;
    private PurgeFullPageCache $purgeFullPageCache;

    public function __construct(
        RecordingRepositoryInterface $recordingRepository,
        RecordingManagerInterface $RecordingManager,
        PurgeFullPageCache $purgeFullPageCache,
        Context $context
    ) {
        parent::__construct($context);
        $this->recordingRepository = $recordingRepository;
        $this->recordingManager = $RecordingManager;
        $this->purgeFullPageCache = $purgeFullPageCache;
    }

    public function execute()
    {
        $recordingId = (int) $this->getRequest()->getParam('id');
        try {
            $recording = $this->recordingRepository->getById($recordingId);
            $this->recordingManager->stop($recording);
            $this->purgeFullPageCache->execute();
            $this->messageManager->addSuccessMessage(__('The recording was stopped successfully.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('An error occurred while stopping the recording.'));
        }
        $pageResult = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $pageResult->setPath('*/*/view/', [ 'id' => $recordingId ]);
        return $pageResult;
    }
}
