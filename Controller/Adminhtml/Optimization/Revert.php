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
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\OptimizationManagerInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Model\PurgeFullPageCache;

class Revert extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Mageproxy_Connector::optimization_revert';

    private OptimizationManagerInterface $optimizationManager;
    private OptimizationRepositoryInterface $optimizationRepository;
    private PurgeFullPageCache $purgeFullPageCache;

    public function __construct(
        Context $context,
        OptimizationManagerInterface $optimizationManager,
        OptimizationRepositoryInterface $optimizationRepository,
        PurgeFullPageCache $purgeFullPageCache
    ) {
        parent::__construct($context);
        $this->optimizationManager = $optimizationManager;
        $this->optimizationRepository = $optimizationRepository;
        $this->purgeFullPageCache = $purgeFullPageCache;
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $redirect = $this->resultRedirectFactory->create();
        $optimizationId = (int) $params['optimization_id'] ?? null;

        try {
            $optimization = $this->optimizationRepository->getById($optimizationId);
            $recordingId = $optimization->getRecordingId();
            $redirect->setPath('*/recording/view', [
                'id' => $recordingId,
            ]);
            if ($optimization->getRecording()->getInitiator() === RecordingInterface::INITIATOR_CRON) {
                // Manually reverting a cron based optimization, goes straight to finished, otherwise
                // it will be picked up again for deployment on the next cron run
                $this->optimizationManager->revert($optimization, OptimizationInterface::STATUS_FINISHED);
            } else {
                $this->optimizationManager->revert($optimization);
            }
            $this->purgeFullPageCache->execute(true);
            $this->getMessageManager()->addSuccessMessage(__('The optimization deployment was reverted'));
        } catch (NoSuchEntityException $e) {
            $this->getMessageManager()->addErrorMessage(__('The optimization was not found'));
        } catch (\Exception $e) {
            $this->getMessageManager()->addErrorMessage(__('An error occurred while reverting the optimization'));
        }

        return $redirect;
    }
}
