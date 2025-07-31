<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Api\Data;

/**
 * @api
 */
interface RecordingInterface
{
    const RECORDING_ID = 'recording_id';
    const UUID = 'uuid';
    const CREATED_AT = 'created_at';
    const SCHEDULED_AT = 'scheduled_at';
    const STARTED_AT = 'started_at';
    const FINISHED_AT = 'finished_at';
    const STATUS = 'status';
    const DURATION = 'duration';
    const STORE_ID = 'store_id';
    const INCLUDE_TIMESTAMP = 'include_timestamp';
    const ERROR_MESSAGE = 'error_msg';
    const STATIC_VERSION = 'static_version';
    const START_COUNT = 'start_count';
    const RECORD_SCHEDULE = 'record_schedule';
    const INITIATOR = 'initiator';
    const DEPS_CNT = 'deps_cnt';
    const HDLS_CNT = 'hdls_cnt';
    const PAGE_HANDLE_PRIORITY = 'page_handle_priority';
    const AUTO_RUN_TYPE = 'auto_run_type';
    const CHECKSUM = 'checksum';

    /*
     * Ready to start running at scheduled time
     */
    public const STATUS_PENDING = 0;

    /*
     * Currently running, first status if the recording was not scheduled
     */
    public const STATUS_RUNNING = 1;

    /*
     * Stopped by user or cron, can be restarted
     */
    public const STATUS_STOPPED = 2;

    /*
     * Recording can no longer be started, static deployment no longer available
     */
    public const STATUS_FINISHED = 3;

    /*
     * Never started, missed the recording interval
     */
    public const STATUS_MISSED = 4;

    /*
     * An error occurred during the recording
     */
    public const STATUS_ERROR = 5;

    /*
     * Schedule the recording
     */
    public const MODE_SCHEDULED = 'scheduled';

    /*
     * Run now
     */
    public const MODE_IMMEDIATE = 'immediate';

    /*
     * Recording was created by the cron in auto mode
     */
    public const INITIATOR_CRON = 'cron';

    /*
     * Recording was initiated by an admin user
     *
     */
    public const INITIATOR_USER = 'user';

    /**
     * Default duration
     */
    public const DEFAULT_DURATION = 5;

    /**
     * Option for All store views processing
     */
    public const ALL_STORE_VIEWS = 0;

    /**
     * @return int|null
     */
    public function getRecordingId(): ?int;

    /**
     * @return array
     */
    public function getRecordSchedule(): array;

    /**
     * @param array|null $recordSchedule
     * @return void
     */
    public function setRecordSchedule(?array $recordSchedule): void;

    /**
     * Total times the recording was started including the initial run
     * @return int|null
     */
    public function getStartCount(): ?int;

    /**
     * Total times the recording was restarted
     *
     * @return int|null
     */
    public function getRestartCount(): ?int;

    /**
     * @param int $startCount
     * @return void
     */
    public function setStartCount(int $startCount): void;

    /**
     * @param int $recordingId
     * @return void
     */
    public function setRecordingId(int $recordingId): void;

    /**
     * @return string|null
     */
    public function getUuid(): ?string;

    /**
     * @param string $uuid
     * @return void
     */
    public function setUuid(string $uuid): void;

    /**
     * @param string $scheduledAt
     * @return void
     */
    public function setScheduledAt(string $scheduledAt): void;

    /**
     * @return string|null
     */
    public function getScheduledAt(): ?string;

    /**
     * @param int $status
     * @return void
     */
    public function setStatus(int $status): void;

    /**
     * @return int
     */
    public function getStatus(): int;

    /**
     * @return int
     */
    public function getDuration(): int;

    /**
     * @param int $duration
     * @return void
     */
    public function setDuration(int $duration): void;

    /**
     * @return string|null
     */
    public function getStartedAt(): ?string;

    /**
     * @param string $startedAt
     * @return void
     */
    public function setStartedAt(string $startedAt): void;

    /**
     * @return int
     */
    public function getStoreId(): int;

    /**
     * @param int $storeId
     * @return void
     */
    public function setStoreId(int $storeId): void;

    /**
     * @return bool
     */
    public function getIncludeTimestamp(): bool;

    /**
     * @param bool $includeTimestamp
     * @return void
     */
    public function setIncludeTimestamp(bool $includeTimestamp): void;

    /**
     * @return string|null
     */
    public function getFinishedAt(): ?string;

    /**
     * @param string|null $finishedAt
     * @return void
     */
    public function setFinishedAt(?string $finishedAt): void;

    /**
     * @return string
     */
    public function getStaticVersion(): string;

    /**
     * @param string $staticVersion
     * @return void
     */
    public function setStaticVersion(string $staticVersion): void;

    /**
     * @param string $errorMessage
     * @return void
     */
    public function setErrorMessage(string $errorMessage): void;

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string;

    /**
     * @return string
     */
    public function getInitiator(): string;

    /**
     * @param string $initiator
     * @return void
     */
    public function setInitiator(string $initiator): void;

    /**
     * @return int
     */
    public function getDepsCnt(): int;

    /**
     * @param int $depsCnt
     * @return void
     */
    public function setDepsCnt(int $depsCnt): void;

    /**
     * @return array
     */
    public function getPageHandlePriority(): array;

    /**
     * @param array $pageHandlePriority
     * @return void
     */
    public function setPageHandlePriority(array $pageHandlePriority): void;

    /**
     * @param string $autoRunType
     * @return void
     */
    public function setAutoRunType(string $autoRunType): void;

    /**
     * @return string|null
     */
    public function getAutoRunType(): ?string;

    /**
     * @return int
     */
    public function getHdlsCnt(): int;

    /**
     * @param int $hdlsCnt
     * @return void
     */
    public function setHdlsCnt(int $hdlsCnt): void;

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * @param string $createdAt
     * @return void
     */
    public function setCreatedAt(string $createdAt): void;

    /**
     * @return string
     */
    public function getChecksum(): ?string;

    /**
     * @param string $checksum
     * @return void
     */
    public function setChecksum(string $checksum): void;
}
