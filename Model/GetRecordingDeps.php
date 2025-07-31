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

use Magento\Framework\Exception\LocalizedException;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Model\ApiClient\GetRecordingJsDepsCountInterface;
use Mageproxy\Connector\Model\ApiClient\GetRecordingJsDepsInterface;
use Mageproxy\Connector\Model\ResourceModel\Dependency as ResourceModel;

class GetRecordingDeps
{
    private GetRecordingJsDepsInterface $recordingJsDepsApiClient;
    private ResourceModel $resource;
    private RecordingRepositoryInterface $recordingRepository;
    private GetRecordingJsDepsCountInterface $recordingJsDepsCountApiClient;

    public function __construct(
        GetRecordingJsDepsInterface $recordingJsDepsApiClient,
        RecordingRepositoryInterface $recordingRepository,
        GetRecordingJsDepsCountInterface $recordingJsDepsCountApiClient,
        ResourceModel $resource
    ) {
        $this->recordingJsDepsApiClient = $recordingJsDepsApiClient;
        $this->resource = $resource;
        $this->recordingRepository = $recordingRepository;
        $this->recordingJsDepsCountApiClient = $recordingJsDepsCountApiClient;
    }

    public function execute(int $recordingId): void
    {
        try {
            $recording = $this->recordingRepository->getById($recordingId);
            $remoteDepsCnt = $this->recordingJsDepsCountApiClient->execute($recording->getUuid());
            $dependencies = $this->recordingJsDepsApiClient->execute($recording->getUuid());
            if (!empty($dependencies)) {
                $this->resource->insertDepsByHandle(
                    $dependencies,
                    (int) $recording->getId(),
                    $recording->getPageHandlePriority()
                );
                $recording->setDepsCnt($remoteDepsCnt->getCount());
                $recording->setHdlsCnt($remoteDepsCnt->getHdlsCount());
                $this->recordingRepository->save($recording);
            }
        } catch (\Exception $e) {
            throw new LocalizedException(
                __('An error occurred while fetching the recording dependencies: %1', $e->getMessage())
            );
        }
    }
}
