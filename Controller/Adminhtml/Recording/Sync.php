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
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Model\GetRecordingDeps;
use Mageproxy\Connector\Model\SyncOptimization;

/**
 * Fetch all the latest information from the API for the recording
 * and any associated optimizations
 */
class Sync extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Mageproxy_Connector::recording_view';

    private GetRecordingDeps $getRecordingJsDeps;
    private OptimizationRepositoryInterface $optimizationRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private SyncOptimization $syncOptimization;
    private RecordingRepositoryInterface $repository;

    public function __construct(
        Context $context,
        GetRecordingDeps $getRecordingJsDeps,
        OptimizationRepositoryInterface $optimizationRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SyncOptimization $syncOptimization,
        RecordingRepositoryInterface $repository
    ) {
        parent::__construct($context);
        $this->getRecordingJsDeps = $getRecordingJsDeps;
        $this->optimizationRepository = $optimizationRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->syncOptimization = $syncOptimization;
        $this->repository = $repository;
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

        try {
            $recording = $this->repository->getById($recordingId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('The recording does not exist.'));
            $redirect->setPath('*/*/');
            return $redirect;
        }

        $redirect->setPath('*/*/view/', [
            'id' => $recordingId
        ]);

        try {
            $this->getRecordingJsDeps->execute($recordingId);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $redirect;
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('An error occurred while synchronizing the recording.'));
            return $redirect;
        }

        $this->searchCriteriaBuilder
            ->addFilter('recording_id', $recordingId)
            ->addFilter('status', [
                OptimizationInterface::STATUS_PUBLISHED,
                OptimizationInterface::STATUS_REQUESTED
            ], 'in');
        $optimizations = $this->optimizationRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        foreach ($optimizations as $optimization) {
            try {
                $this->syncOptimization->execute($optimization);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $redirect;
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('An error occurred while synchronizing the optimization.')
                );
                return $redirect;
            }
        }

        $this->messageManager->addSuccessMessage(
            __('The recording data was successfully synchronized from the MageProxy API.')
        );
        return $redirect;
    }
}
