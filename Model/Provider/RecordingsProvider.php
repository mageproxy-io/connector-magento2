<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Model\Provider;

use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Model\ProviderInterface;

class RecordingsProvider implements ProviderInterface
{
    /**
     * @var \Mageproxy\Connector\Model\Provider\SearchCriteriaProvider
     */
    private SearchCriteriaProvider $searchCriteriaProvider;

    /**
     * @var \Mageproxy\Connector\Api\RecordingRepositoryInterface
     */
    private RecordingRepositoryInterface $recordingRepository;

    public function __construct(
        SearchCriteriaProvider $searchCriteriaProvider,
        RecordingRepositoryInterface $recordingRepository
    ) {
        $this->searchCriteriaProvider = $searchCriteriaProvider;
        $this->recordingRepository = $recordingRepository;
    }

    public function getItems(): array
    {
        $result = $this->recordingRepository->getList(
            $this->searchCriteriaProvider->getSearchCriteria()
        );
        return $result->getItems();
    }
}
