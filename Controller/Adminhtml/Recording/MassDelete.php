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
use Mageproxy\Connector\Api\OptimizationRepositoryInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Model\ResourceModel\Recording\CollectionFactory;

class MassDelete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Mageproxy_Connector::recording_delete';

    private RecordingRepositoryInterface $recordingRepository;
    private Filter $filter;
    private CollectionFactory $collectionFactory;
    private OptimizationRepositoryInterface $optimizationRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private RedirectFactory $redirectFactory;

    public function __construct(
        Context $context,
        RecordingRepositoryInterface $recordingRepository,
        Filter $filter,
        CollectionFactory $collectionFactory,
        PageFactory $resultFactory,
        OptimizationRepositoryInterface $optimizationRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RedirectFactory $redirectFactory
    ) {
        parent::__construct($context);
        $this->recordingRepository = $recordingRepository;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->resultFactory = $resultFactory;
        $this->optimizationRepository = $optimizationRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->redirectFactory = $redirectFactory;
    }

    public function execute()
    {
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
            $this->recordingRepository->deleteById($id);
            $deleteCnt++;
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $deleteCnt)
        );

        return $this->redirectFactory->create()->setUrl($this->getUrl('*/*/'));
    }
}
