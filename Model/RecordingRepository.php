<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model;

use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\Data\RecordingInterfaceFactory;
use Mageproxy\Connector\Api\Data\RecordingSearchResultsInterface;
use Mageproxy\Connector\Api\Data\RecordingSearchResultsInterfaceFactory;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Model\ResourceModel\Recording\CollectionFactory;

class RecordingRepository implements RecordingRepositoryInterface
{
    private RecordingSearchResultsInterfaceFactory $searchResultsFactory;
    private CollectionProcessorInterface $collectionProcessor;
    private ResourceModel\Recording\CollectionFactory $collectionFactory;
    private RecordingInterfaceFactory $recordingFactory;
    private ResourceModel\Recording $resourceModel;

    public function __construct(
        RecordingSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        CollectionFactory $collectionFactory,
        RecordingInterfaceFactory $recordingFactory,
        \Mageproxy\Connector\Model\ResourceModel\Recording $resourceModel
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->collectionFactory = $collectionFactory;
        $this->recordingFactory = $recordingFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * @inheritdoc
     */
    public function save(RecordingInterface $recording): RecordingInterface
    {
        try {
            $this->resourceModel->save($recording);
        } catch (Exception $e) {
            throw new CouldNotSaveException(
                __('Could not save the recording: %1', $e->getMessage()),
                $e
            );
        }
        return $recording;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $id): bool
    {
        try {
            $recording = $this->getById($id);
            return $this->delete($recording);
        } catch (NoSuchEntityException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new CouldNotDeleteException(
                __('Could not delete the recording with id "%1"', $id),
                $e
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function get(string $uuid): RecordingInterface
    {
        $recording = $this->recordingFactory->create();
        $this->resourceModel->load($recording, $uuid, RecordingInterface::UUID);
        if ($recording->getId() === null) {
            throw new NoSuchEntityException(
                __('Recording with uuid "%1" does not exist.', $uuid)
            );
        }
        return $recording;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $id): RecordingInterface
    {
        $recording = $this->recordingFactory->create();
        $this->resourceModel->load($recording, $id);
        if ($recording->getId() === null) {
            throw new NoSuchEntityException(
                __('Recording with id "%1" does not exist.', $id)
            );
        }
        return $recording;
    }

    public function delete(RecordingInterface $recording): bool
    {
        try {
            $this->resourceModel->delete($recording);
        } catch (Exception $e) {
            throw new CouldNotDeleteException(
                __('Could not delete the recording with id "%1"', $recording->getId()),
                $e
            );
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): RecordingSearchResultsInterface
    {
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setTotalCount($collection->getSize());
        $searchResult->setItems($collection->getItems());

        return $searchResult;
    }
}
