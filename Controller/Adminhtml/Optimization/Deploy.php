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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageproxy\Connector\Api\OptimizationManagerInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Model\PurgeFullPageCache;

class Deploy extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Mageproxy_Connector::optimization_deploy';

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
        $redirect = $this->resultRedirectFactory->create();
        $id = (int) $this->getRequest()->getParam('optimization_id');
        try {
            $optimization = $this->optimizationRepository->getById($id);
            $recordingId = $optimization->getRecordingId();
            $redirect->setPath('*/recording/view', [ 'id' => $recordingId ]);
        } catch (NoSuchEntityException $e) {
            $this->getMessageManager()->addErrorMessage(__('Unable to deploy optimization. Optimization could not be found.'));
            return $redirect->setRefererUrl();
        }
        try {
            $this->optimizationManager->deploy($optimization);
            $this->purgeFullPageCache->execute(true);
        } catch (LocalizedException $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
            return $redirect;
        }
        $this->getMessageManager()->addSuccessMessage(
            __('The optimization was deployed. Select "Revert" do undo the deployment.')
        );
        return $redirect;
    }
}
