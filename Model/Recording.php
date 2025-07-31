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

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Mageproxy\Connector\Api\Data\RecordingInterface;

class Recording extends AbstractModel implements RecordingInterface, IdentityInterface
{
    /**
     * Event handling properties
     */
    protected $_eventPrefix = 'mageproxy_recording';
    protected $_eventObject = 'recording';

    protected function _construct()
    {
        $this->_init(\Mageproxy\Connector\Model\ResourceModel\Recording::class);
    }

    /**
     * @inheritdoc
     */
    public function getRecordingId(): ?int
    {
        $id = $this->getData(self::RECORDING_ID);
        return $id ? (int) $id : null;
    }

    /**
     * @inheritdoc
     */
    public function setRecordingId(int $recordingId): void
    {
        $this->setData(self::RECORDING_ID, $recordingId);
    }

    /**
     * @inheritdoc
     */
    public function getUuid(): ?string
    {
        return $this->getData(self::UUID);
    }

    /**
     * @inheritdoc
     */
    public function setUuid(string $uuid = null): void
    {
        $this->setData(self::UUID, $uuid);
    }

    /**
     * @inheritdoc
     */
    public function setScheduledAt(string $scheduledAt): void
    {
        $this->setData(self::SCHEDULED_AT, $scheduledAt);
    }

    /**
     * @inheritdoc
     */
    public function getScheduledAt(): ?string
    {
        return $this->getData(self::SCHEDULED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setStatus(int $status): void
    {
        $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritdoc
     */
    public function getStatus(): int
    {
        return (int) $this->getData(self::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function getDuration(): int
    {
        return (int) $this->getData(self::DURATION);
    }

    /**
     * @inheritdoc
     */
    public function setDuration(int $duration): void
    {
        $this->setData(self::DURATION, $duration);
    }

    /**
     * @inheritdoc
     */
    public function getIdentities(): array
    {
        return ['FPC'];
    }

    /**
     * @inheritdoc
     */
    public function getStartedAt(): ?string
    {
        return $this->getData(self::STARTED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setStartedAt(string $startedAt): void
    {
        $this->setData(self::STARTED_AT, $startedAt);
    }

    /**
     * @inheritdoc
     */
    public function getStoreId(): int
    {
        return (int) $this->getData(self::STORE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStoreId(int $storeId): void
    {
        $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @inheritdoc
     */
    public function getIncludeTimestamp(): bool
    {
        return (bool) $this->getData(self::INCLUDE_TIMESTAMP);
    }

    /**
     * @inheritdoc
     */
    public function setIncludeTimestamp(bool $includeTimestamp): void
    {
        $this->setData(self::INCLUDE_TIMESTAMP, $includeTimestamp);
    }

    /**
     * @inheritdoc
     */
    public function getFinishedAt(): ?string
    {
        return $this->getData(self::FINISHED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setFinishedAt(?string $finishedAt): void
    {
        $this->setData(self::FINISHED_AT, $finishedAt);
    }

    /**
     * @inheritdoc
     */
    public function setErrorMessage(string $errorMessage): void
    {
        $this->setData(self::ERROR_MESSAGE, $errorMessage);
    }

    /**
     * @inheritdoc
     */
    public function getErrorMessage(): ?string
    {
        return $this->getData(self::ERROR_MESSAGE);
    }

    /**
     * @inheritdoc
     */
    public function getStaticVersion(): string
    {
        return (string) $this->getData(self::STATIC_VERSION);
    }

    /**
     * @inheritdoc
     */
    public function setStaticVersion(string $staticVersion): void
    {
        $this->setData(self::STATIC_VERSION, $staticVersion);
    }

    /**
     * @inheritdoc
     */
    public function getStartCount(): ?int
    {
        return (int) $this->getData(self::START_COUNT);
    }

    /**
     * @inheritdoc
     */
    public function setStartCount(int $startCount): void
    {
        $this->setData(self::START_COUNT, $startCount);
    }

    /**
     * @inheritdoc
     */
    public function getRecordSchedule(): array
    {
        $schedule = $this->getData(self::RECORD_SCHEDULE) ?? [];
        if (is_string($schedule)) {
            $schedule = json_decode($schedule, true);
        }
        return $schedule;
    }

    /**
     * @inheritdoc
     */
    public function setRecordSchedule(?array $recordSchedule): void
    {
        $this->setData(self::RECORD_SCHEDULE, $recordSchedule);
    }

    /**
     * @inheritdoc
     */
    public function getRestartCount(): ?int
    {
        return $this->getStartCount() - 1;
    }

    /**
     * @inheritdoc
     */
    public function getInitiator(): string
    {
        return $this->getData(self::INITIATOR);
    }

    /**
     * @inheritdoc
     */
    public function setInitiator(string $initiator): void
    {
        $this->setData(self::INITIATOR, $initiator);
    }

    /**
     * @inheritdoc
     */
    public function getDepsCnt(): int
    {
        return (int) $this->getData(self::DEPS_CNT);
    }

    /**
     * @inheritdoc
     */
    public function setDepsCnt(int $depsCnt): void
    {
        $this->setData(self::DEPS_CNT, $depsCnt);
    }

    /**
     * @inheritdoc
     */
    public function getPageHandlePriority(): array
    {
        return $this->getData(self::PAGE_HANDLE_PRIORITY);
    }

    /**
     * @inheritdoc
     */
    public function setPageHandlePriority(array $pageHandlePriority): void
    {
        $this->setData(self::PAGE_HANDLE_PRIORITY, $pageHandlePriority);
    }

    /**
     * @inheritdoc
     */
    public function setAutoRunType(string $autoRunType): void
    {
        $this->setData(self::AUTO_RUN_TYPE, $autoRunType);
    }

    /**
     * @inheritdoc
     */
    public function getAutoRunType(): ?string
    {
        return $this->getData(self::AUTO_RUN_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function getHdlsCnt(): int
    {
        return (int) $this->getData(self::HDLS_CNT);
    }

    /**
     * @inheritdoc
     */
    public function setHdlsCnt(int $hdlsCnt): void
    {
        $this->setData(self::HDLS_CNT, $hdlsCnt);
    }

    public function getLifetime(): int
    {
        if ($this->getFinishedAt()) {
            return 0;
        }

        return $this->getDuration() * 60 - (time() - strtotime($this->getStartedAt()));
    }

    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->setData(self::CREATED_AT, $createdAt);
    }

    public function getChecksum(): ?string
    {
        return $this->getData(self::CHECKSUM);
    }

    public function setChecksum(string $checksum): void
    {
        $this->setData(self::CHECKSUM, $checksum);
    }
}
