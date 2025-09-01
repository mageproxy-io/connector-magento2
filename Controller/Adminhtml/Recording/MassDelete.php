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
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageproxy\Connector\Api\Data\OptimizationInterface;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Api\RecordingManagerInterface;
use Mageproxy\Connector\Model\ResourceModel\Recording\CollectionFactory;

class MassDelete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Mageproxy_Connector::recording_delete';

    private Filter $filter;
    private CollectionFactory $collectionFactory;
    private OptimizationRepositoryInterface $optimizationRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private RedirectFactory $redirectFactory;
    private RecordingManagerInterface $recordingManager;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        PageFactory $resultFactory,
        OptimizationRepositoryInterface $optimizationRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RedirectFactory $redirectFactory,
        RecordingManagerInterface $recordingManager
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->resultFactory = $resultFactory;
        $this->optimizationRepository = $optimizationRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->redirectFactory = $redirectFactory;
        $this->recordingManager = $recordingManager;
    }

    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());

            $this->searchCriteriaBuilder->addFilter(
                OptimizationInterface::STATUS,
                OptimizationInterface::STATUS_DEPLOYED
            );
            $result = $this->optimizationRepository->getList(
                $this->searchCriteriaBuilder->create()
            );
            $deployedRecordingIds = [];
            if ($result->getTotalCount() > 0) {
                $items = $result->getItems();
                $deployedRecordingIds = array_map(function (OptimizationInterface $optimization) {
                    return (int) $optimization->getRecordingId();
                }, array_values($items));
            }

            $deleteCnt = 0;
            foreach ($collection as $id => $recording) {
                if (in_array($id, $deployedRecordingIds)) {
                    $this->messageManager->addErrorMessage(
                        __(
                            'The recording with ID "%1" is deployed and cannot be deleted. '
                            . 'First revert the deployment before deleting.',
                            $recording->getUuid()
                        )
                    );
                    continue;
                }
                if ($recording->getStatus() === RecordingInterface::STATUS_RUNNING) {
                    $this->messageManager->addErrorMessage(
                        __(
                            'The recording with ID "%1" is currently running and cannot be deleted. '
                            . 'First stop the recording before deleting.',
                            $recording->getUuid()
                        )
                    );
                    continue;
                }
                $this->recordingManager->delete($recording);
                $deleteCnt++;
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('An error occurred while deleting the recordings.')
            );
            return $this->redirectFactory->create()->setUrl($this->getUrl('*/*/'));
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $deleteCnt)
        );

        return $this->redirectFactory->create()->setUrl($this->getUrl('*/*/'));
    }
}
