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

use DateTime;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\View\Deployment\Version;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Stdlib\DateTime as StdLibDateTime;
use Magento\Store\Model\StoreManagerInterface;
use Mageproxy\Connector\Api\Data\RecordingInterface;
use Mageproxy\Connector\Api\RecordingManagerInterface;
use Mageproxy\Connector\Api\RecordingRepositoryInterface;
use Mageproxy\Connector\Model\ApiClient\DeleteRecordingInterface;
use Mageproxy\Connector\Model\ApiClient\Exception\ApiException;
use Mageproxy\Connector\Model\ApiClient\GetRecordingJsDepsCountInterface;
use Mageproxy\Connector\Model\ApiClient\GetRecordingSnapshotInterface;
use Mageproxy\Connector\Model\ApiClient\PostRecordingStartInterface;
use Mageproxy\Connector\Model\Recording\Source\Status;
use Mageproxy\Connector\Model\ResourceModel\Dependency\CollectionFactory;
use Mageproxy\Connector\Model\System\Config\Source\AutoRunType;
use Mageproxy\Connector\Model\System\Config\Source\RunMode;

class RecordingManager implements RecordingManagerInterface
{
    private RecordingRepositoryInterface $recordingRepository;
    private DeleteRecordingInterface $deleteRecordingApiClient;
    private Status $statusOptions;
    private StoreManagerInterface $storeManager;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private Config $config;
    private StdLibDateTime $dateTimeFmt;
    private Version $deployedVersion;
    private PostRecordingStartInterface $postRecordingStart;
    private RecordingFactory $recordingFactory;
    private AutoRunScheduleFactory $autoRunScheduleFactory;
    private CollectionFactory $dependencyCollectionFactory;
    private GetRecordingJsDepsCountInterface $getRecordingJsDepsCountApiClient;
    private GetRecordingSnapshotInterface $getRecordingSnapshotApiClient;
    private PurgeFullPageCache $purgeFullPageCache;

    public function __construct(
        RecordingRepositoryInterface $recordingRepository,
        Status $statusOptions,
        StoreManagerInterface $storeManager,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Config $config,
        StdLibDateTime $dateTimeFmt,
        Version $deployedVersion,
        PurgeFullPageCache $purgeFullPageCache,
        PostRecordingStartInterface $postRecordingStart,
        RecordingFactory $recordingFactory,
        AutoRunScheduleFactory $autoRunScheduleFactory,
        CollectionFactory $dependencyCollectionFactory,
        GetRecordingJsDepsCountInterface $getRecordingJsDepsCountApiClient,
        GetRecordingSnapshotInterface $getRecordingSnapshotApiClient,
        DeleteRecordingInterface $deleteRecordingApiClient,
    ) {
        $this->recordingRepository = $recordingRepository;
        $this->statusOptions = $statusOptions;
        $this->storeManager = $storeManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->config = $config;
        $this->dateTimeFmt = $dateTimeFmt;
        $this->deployedVersion = $deployedVersion;
        $this->purgeFullPageCache = $purgeFullPageCache;
        $this->postRecordingStart = $postRecordingStart;
        $this->recordingFactory = $recordingFactory;
        $this->autoRunScheduleFactory = $autoRunScheduleFactory;
        $this->dependencyCollectionFactory = $dependencyCollectionFactory;
        $this->getRecordingJsDepsCountApiClient = $getRecordingJsDepsCountApiClient;
        $this->getRecordingSnapshotApiClient = $getRecordingSnapshotApiClient;
        $this->deleteRecordingApiClient = $deleteRecordingApiClient;
    }

    /**
     * @inheritdoc
     */
    public function shouldStart(RecordingInterface $recording): bool
    {
        if ($recording->getStatus() !== RecordingInterface::STATUS_PENDING) {
            return false;
        }
        $now = time();

        return $now >= $this->getRecordingStartsAtEpoch($recording)
            && $now <= $this->getRecordingEndsAtEpoch($recording);
    }

    /**
     * @inheritdoc
     */
    public function shouldRestart(RecordingInterface $recording): bool
    {
        if ($this->config->getRunMode($recording->getStoreId()) !== RunMode::MODE_AUTO) {
            return false;
        }
        if ($recording->getStatus() !== RecordingInterface::STATUS_STOPPED) {
           return false;
        }
        $schedule = $this->autoRunScheduleFactory->create([
            'schedule' => $recording->getRecordSchedule()
        ]);

        $startCount = $recording->getStartCount(); // how many times has the recording been started
        if ($startCount >= $schedule->getIterations()) {
            return false; // if it's been started at least as many times as scheduled, no restart..
        }

        // When was it last finished
        $finishedAtEpoch = strtotime($recording->getFinishedAt());
        // How long should it be paused for given a certain iteration
        $pauseInterval = $schedule->getPauseForDuration($startCount, AutoRunSchedule::SECONDS);
        // When should it be restarted
        $restartEpoch = $finishedAtEpoch + $pauseInterval;
        // Has that time come?
        return time() > $restartEpoch;
    }

    /**
     * @inheritdoc
     */
    public function shouldStop(RecordingInterface $recording): bool
    {
        return $recording->getStatus() === RecordingInterface::STATUS_RUNNING
            && time() > $this->getRecordingEndsAtEpoch($recording);
    }

    /**
     * @inheritdoc
     */
    public function stop(RecordingInterface $recording, bool $flushCache = false): void
    {
        if ($recording->getStatus() === RecordingInterface::STATUS_STOPPED) {
            throw new LocalizedException(
                __('Recording is already stopped')
            );
        }
        if ($recording->getStatus() !== RecordingInterface::STATUS_RUNNING) {
            throw new LocalizedException(
                __('Recording in status "%1" can not be stopped',
                    $this->statusOptions->getLabel($recording->getStatus())
                )
            );
        }

        $recording->setStatus(RecordingInterface::STATUS_STOPPED);
        $recording->setFinishedAt($this->dateTimeFmt->formatDate(time()));
        $this->recordingRepository->save($recording);
        $this->purgeFullPageCache->shouldPurge($this->config->getRecordingFlushFpc());
    }

    /**
     * @inheritdoc
     */
    public function start(RecordingInterface $recording): void
    {
        if ($recording->getStatus() === RecordingInterface::STATUS_RUNNING) {
            throw new LocalizedException(
                __('Recording is already running')
            );
        }

        if ($this->deployedVersion->getValue() !== $recording->getStaticVersion()) {
            throw new LocalizedException(
                __('A newer version has been deployed.')
            );
        }

        if (!in_array($recording->getStatus(), [
            RecordingInterface::STATUS_PENDING,
            RecordingInterface::STATUS_STOPPED
        ])) {
            throw new LocalizedException(
                __('Recording in status "%1" can not be started',
                    $this->statusOptions->getLabel($recording->getStatus())
                )
            );
        }

        if ($this->getRunning($recording->getStoreId()) !== null) {
            throw new LocalizedException(
                __('Another recording is already running for the same store')
            );
        }

        if ($recording->getStatus() === RecordingInterface::STATUS_STOPPED) {
            // restart immediately
            $recording->setStatus(RecordingInterface::STATUS_RUNNING);
            $recording->setFinishedAt(null);
            $start = $this->dateTimeFmt->formatDate(time());
            $recording->setScheduledAt($start);
            $recording->setStartedAt($start);
        } else { // pending (scheduled recording)
            $recording->setStatus(RecordingInterface::STATUS_RUNNING);
            $recording->setStartedAt($this->dateTimeFmt->formatDate(time()));
        }

        $startCount = $recording->getStartCount() ?? 0;
        $startCount++;
        $recording->setStartCount($startCount);

        // Duration can be variable depending on auto run schedule
        if ($this->config->getAutoRunType($recording->getStoreId()) === AutoRunType::SCHEDULED
            && !empty($recording->getRecordSchedule())
        ) {
            $schedule = $this->autoRunScheduleFactory->create([
                'schedule' => $recording->getRecordSchedule()
            ]);
            $recording->setDuration($schedule->getRecordForDuration($startCount));
        }

        // $this->postRecordingStart->execute($recording->getUuid(), $recording->getDuration());

        $this->recordingRepository->save($recording);
        $this->purgeFullPageCache->shouldPurge($this->config->getRecordingFlushFpc());
    }

    /**
     * @inheritdoc
     */
    public function missed(RecordingInterface $recording): void
    {
        if ($recording->getStatus() !== RecordingInterface::STATUS_PENDING) {
            throw new LocalizedException(__('Recording is not pending'));
        }
        $recording->setStatus(RecordingInterface::STATUS_MISSED);
        $this->recordingRepository->save($recording);
    }

    /**
     * @inheritdoc
     */
    public function shouldFinish(RecordingInterface $recording): bool
    {
        if ($recording->getStatus() === RecordingInterface::STATUS_FINISHED) {
            return false;
        }
        $initiator = $recording->getInitiator();
        $runMode = $this->config->getRunMode($recording->getStoreId());

        if (
            ($initiator === RecordingInterface::INITIATOR_USER && $runMode === RunMode::MODE_AUTO)
            || ($initiator === RecordingInterface::INITIATOR_CRON && $runMode === RunMode::MODE_MANUAL)
        ) {
            // The way the recording was initiated vs the current run mode do not longer match
            return true;
        }

        $recordingStaticDeploymentVersion = $recording->getStaticVersion();
        $deployedStaticVersion = (string) $this->deployedVersion->getValue();
        // New static content deployment has taken place
        return $recordingStaticDeploymentVersion !== $deployedStaticVersion;
    }

    /**
     * @inheritdoc
     */
    public function finish(RecordingInterface $recording): void
    {
        if ($recording->getStatus() === RecordingInterface::STATUS_RUNNING) {
            $this->stop($recording);
        }
        $recording->setStatus(RecordingInterface::STATUS_FINISHED);
        $this->recordingRepository->save($recording);
        try {
            $this->deleteRecordingApiClient->execute($recording->getUuid());
        } catch (\Exception $e) {
            // Might throw if recording already deleted
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(RecordingInterface $recording): void
    {
        $uuid = $recording->getUuid();
        $this->recordingRepository->delete($recording);
        $this->deleteRecordingApiClient->execute($uuid);
    }

    /**
     * @inheritdoc
     */
    public function error(RecordingInterface $recording, string $message): void
    {
        $recording->setStatus(RecordingInterface::STATUS_ERROR);
        $recording->setErrorMessage($message);
        try {
            $this->recordingRepository->save($recording);
        } catch (CouldNotSaveException $e) {
        }
    }

    /**
     * @inheritdoc
     */
    public function wasMissed(RecordingInterface $recording): bool
    {
        $scheduledAtEpoch = DateTime::createFromFormat(
            StdLibDateTime::DATETIME_PHP_FORMAT,
            $recording->getScheduledAt()
        )->getTimestamp();

        return $recording->getStatus() === RecordingInterface::STATUS_PENDING
            && time() > $scheduledAtEpoch + $recording->getDuration() * 60;
    }

    /**
     * @inheritdoc
     */
    public function isInProgress(?int $storeId = null): bool
    {
        return $this->getRunning($storeId) !== null;
    }

    /**
     * @inheritdoc
     */
    public function getRunning(?int $storeId = null): ?RecordingInterface
    {
        try {
            $storeId = $storeId ?? $this->storeManager->getStore()->getId();
        } catch (NoSuchEntityException $e) {
            return null;
        }
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(
                'store_id',
                $storeId
            )->addFilter(
                RecordingInterface::STATUS,
                RecordingInterface::STATUS_RUNNING
            )->setPageSize(
                1
            )->setCurrentPage(
                1
            )->create();
        $result = $this->recordingRepository->getList($searchCriteria);
        return $result->getTotalCount() ? current($result->getItems()) : null;
    }

    /**
     * @inheritdoc
     */
    public function getTrackingUrl(): string
    {
        return $this->config->getTrackingUrl();
    }

    public function createNewRecordings(
        array $storeIds,
        ?int $duration = null,
        ?int $scheduledAtTs = null,
        bool $rollBackOnError = false
    ): array {
        $recordings = [];

        foreach ($storeIds as $storeId) {
            $runMode = $this->config->getRunMode($storeId);
            try {
                $recording = $this->recordingFactory->create($storeId, $runMode, $duration, $scheduledAtTs);
                if ($scheduledAtTs) {
                    $this->recordingRepository->save($recording);
                } else {
                    $this->start($recording);
                }
            } catch (\Exception $e) {
                if ($rollBackOnError) {
                    foreach ($recordings as $recording) {
                        $this->recordingRepository->delete($recording);
                    }
                    throw $e;
                }
                $recordings['errors'][$storeId] = $e->getMessage();
                continue;
            }
            $recordings[] = $recording;
        }

        return $recordings;
    }

    private function getRecordingEndsAtEpoch(RecordingInterface $recording): int
    {
        return DateTime::createFromFormat(
            StdLibDateTime::DATETIME_PHP_FORMAT,
            $recording->getStartedAt() ?? $recording->getScheduledAt()
        )->getTimestamp() + $recording->getDuration() * 60;
    }

    private function getRecordingStartsAtEpoch(RecordingInterface $recording): int
    {
        return DateTime::createFromFormat(
            StdLibDateTime::DATETIME_PHP_FORMAT,
            $recording->getScheduledAt()
        )->getTimestamp();
    }

    public function hasDependencies($recording): bool
    {
        if (is_int($recording)) {
            try {
                $recording = $this->recordingRepository->getById($recording);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        if ($recording->getDepsCnt()) {
            return true;
        }

        $collection = $this->dependencyCollectionFactory->create()
            ->addFieldToFilter('recording_id', (int) $recording->getId());

        if (!$collection->getSize()) {
            // Alternatively, fall back to the API to check the count
            try {
                $uuid = $this->recordingRepository->getById((int) $recording->getId())->getUuid();
                if ($this->getRecordingJsDepsCountApiClient->execute($uuid)->getCount() === 0) {
                    return false;
                }
            } catch (NoSuchEntityException|ApiException|NotFoundException $e) {
                return false;
            }
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function populateFromLatestSnapshot(RecordingInterface $recording): void
    {
        try {
            $response = $this->getRecordingSnapshotApiClient->execute($recording->getUuid());
            if (!$response) {
                return;
            }
            $recording->setChecksum($response->getChecksum());
            $recording->setHdlsCnt($response->getHdlsCnt());
            $recording->setDepsCnt($response->getDepsCnt());
        } catch (\Exception $e) {
            return;
        }
    }
}
