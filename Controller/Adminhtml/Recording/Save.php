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

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\RecordingManagerInterface;
use Mageproxy\Connector\Model\Config;
use Mageproxy\Connector\Model\PurgeFullPageCache;
use Mageproxy\Connector\Model\System\Config\Source\RunMode;

class Save extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = "Mageproxy_Connector::recording_create";

    /**
     * @var \Mageproxy\Connector\Api\RecordingManagerInterface
     */
    private RecordingManagerInterface $recordingManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var bool
     */
    private bool $rollBackOnError;

    /**
     * @var \Mageproxy\Connector\Model\PurgeFullPageCache
     */
    private PurgeFullPageCache $purgeFullPageCache;

    /**
     * @var \Mageproxy\Connector\Model\Config
     */
    private Config $config;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Mageproxy\Connector\Api\RecordingManagerInterface $recordingManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Mageproxy\Connector\Model\PurgeFullPageCache $purgeFullPageCache
     * @param \Mageproxy\Connector\Model\Config $config
     * @param bool $rollBackOnError
     */
    public function __construct(
        Context $context,
        RecordingManagerInterface $recordingManager,
        StoreManagerInterface $storeManager,
        PurgeFullPageCache $purgeFullPageCache,
        Config $config,
        bool $rollBackOnError = true
    ) {
        parent::__construct($context);
        $this->recordingManager = $recordingManager;
        $this->storeManager = $storeManager;
        $this->rollBackOnError = $rollBackOnError;
        $this->purgeFullPageCache = $purgeFullPageCache;
        $this->config = $config;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $scheduledAt = $this->getRequest()->getParam('scheduled_at');
        $duration = (int) $this->getRequest()->getParam('duration', 5);

        $storeIds = array_map('intval', $this->getRequest()->getParam('store_id', []));
        $storeIds = array_filter($storeIds);
        $storeIds = array_unique($storeIds);

        $mode = $scheduledAt ? RecordingInterface::MODE_SCHEDULED : RecordingInterface::MODE_IMMEDIATE;

        /**
         * On single store mode, redirect to grid page otherwise redirect to create page, because when single mode
         * is enabled, the save action is forwarded from the create page, making a permanent loop.
         */
        $redirectUrl = $this->storeManager->isSingleStoreMode() ? '*/recording/index/' : '*/recording/create/';

        // Get all store IDs in case of empty array
        $storeIds = empty($storeIds)
            ? array_map(fn($store) => (int) $store->getId(), $this->storeManager->getStores())
            : $storeIds;

        // Filter out stores that are not in manual mode
        $storeIds = array_filter($storeIds, fn($storeId) => $this->config->getRunMode($storeId) === RunMode::MODE_MANUAL);
        if (empty($storeIds)) {
            $this->getMessageManager()->addErrorMessage(__('All requested stores are setup to run in auto run mode.'));
            $this->_redirect('*/*/');
        }

        $scheduledAtTs = $scheduledAt ? strtotime($scheduledAt) : null;
        if ($scheduledAtTs && $scheduledAtTs < time()) {
            $this->getMessageManager()->addErrorMessage(__('Scheduled time must be in the future.'));
            $this->_redirect($redirectUrl, ['mode' => $mode]);
            return;
        }

        try {
            /** @var RecordingInterface[] $recordings */
            $recordings = $this->recordingManager->createNewRecordings(
                $storeIds,
                $duration,
                $scheduledAtTs,
                $this->rollBackOnError
            );
        } catch (Exception $e) {
            $this->getMessageManager()->addErrorMessage(
                __('Failed to create recording(s), error: %1.', $e->getMessage())
            );
            $this->_redirect($redirectUrl, ['mode' => $mode]);
            return;
        }

        if (isset($recordings['errors'])) {
            $errors = $recordings['errors'];
            unset($recordings['errors']);
        }

        $missingStoreIds = array_diff(
            $storeIds,
            array_map(fn(RecordingInterface $recording) => $recording->getStoreId(), $recordings)
        );

        foreach ($missingStoreIds as $storeId) {
            $error = $errors[$storeId] ?? 'Unknown error';
            $this->messageManager->addErrorMessage(
                __('Failed to create recording for store "%1", error: %2', $this->getStoreName($storeId), $error)
            );
        }

        $successMessageTmpl = 'Successfully %1 recording for store "%2".';
        foreach ($recordings as $recording) {
            $store = $this->getStoreName($recording->getStoreId());
            $successMessage = __(
                $successMessageTmpl,
                $recording->getStatus() === RecordingInterface::STATUS_RUNNING ? 'started' : 'scheduled',
                $store
            );
            $this->messageManager->addSuccessMessage($successMessage);
        }

        if (RecordingInterface::MODE_IMMEDIATE === $mode) {
            $this->purgeFullPageCache->execute();
        }

        $this->_redirect('*/*/');
    }

    /**
     * @param int $storeId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getStoreName(int $storeId): string
    {
        return $this->storeManager->getStore($storeId)->getName();
    }
}
