<?php
/**
 * Mageproxy Connector for Magento2.
 *
 * @package    Mageproxy_Connector
 * @copyright  Copyright (c) 2025 Iframe Co Ltd
 * @license    See LICENSE.txt for license details
 */
declare(strict_types=1);

namespace Mageproxy\Connector\Api;

use Mageproxy\Connector\Api\Data\RecordingInterface;

interface RecordingManagerInterface
{
    /**
     * Finish the recording. Cannot be restarted after this
     *
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $recording
     *
     * @return void
     */
    public function finish(\Mageproxy\Connector\Api\Data\RecordingInterface $recording): void;

    /**
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $recording
     * @return bool
     */
    public function shouldFinish(\Mageproxy\Connector\Api\Data\RecordingInterface $recording): bool;

    /**
     * Determine if a recording should be started based on its status and scheduled start time
     *
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $recording
     * @return bool
     */
    public function shouldStart(\Mageproxy\Connector\Api\Data\RecordingInterface $recording): bool;

    /**
     * Determine if the recording needs to be restarted according to the repeat schedule
     *
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $recording
     * @return bool
     */
    public function shouldRestart(\Mageproxy\Connector\Api\Data\RecordingInterface $recording): bool;

    /**
     * Determine if a recording should be stopped based on its status, start time and duration
     *
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $recording
     * @return bool
     */
    public function shouldStop(\Mageproxy\Connector\Api\Data\RecordingInterface $recording): bool;

    /**
     * Determine if a recording was not executed by its scheduled time plus duration
     *
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $recording
     * @return bool
     */
    public function wasMissed(\Mageproxy\Connector\Api\Data\RecordingInterface $recording): bool;

    /**
     * Stops a recording without consideration of schedule
     *
     * Purges full page cache
     *
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $recording
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function stop(\Mageproxy\Connector\Api\Data\RecordingInterface $recording): void;

    /**
     * Start a recording without consideration of schedule
     *
     * Enforces only single recording per store running at any given time
     *
     * Purges full page cache
     *
     * Restarts the recording if it was stopped
     *
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $recording
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function start(\Mageproxy\Connector\Api\Data\RecordingInterface $recording): void;

    /**
     * Updates the recording to missed status
     *
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $recording
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return void
     */
    public function missed(\Mageproxy\Connector\Api\Data\RecordingInterface $recording): void;

    /**
     * Updates the recording to error status and saves the error message
     *
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $recording
     * @param string             $message
     *
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function error(\Mageproxy\Connector\Api\Data\RecordingInterface $recording, string $message): void;

    /**
     * Is there a recording in progress for the given store or the current store if store ID omitted
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isInProgress(?int $storeId = null): bool;

    /**
     * Get the running recording for the provided or current store
     *
     * @param int|null $storeId
     * @return \Mageproxy\Connector\Api\Data\RecordingInterface|null Returns null if no recording is in progress
     */
    public function getRunning(?int $storeId = null): ?\Mageproxy\Connector\Api\Data\RecordingInterface;

    /**
     * @return string
     */
    public function getTrackingUrl(): string;

    /**
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface|int $recording
     * @return bool
     */
    public function hasDependencies($recording): bool;

    /**
     * Create new recordings for the provided stores
     * Starts the recordings immediately if no scheduled time is provided
     * Rolls back any created recordings on error if requested
     *
     * @returns array
     */
    public function createNewRecordings(array $storeIds, int $duration, ?int $scheduledAtTs = null, bool $rollBackOnError = false): array;

    /**
     * Update a snapshot of the recording
     */
    public function populateFromLatestSnapshot(RecordingInterface $recording): void;

    /**
     * Deletes recording locally and remotely
     *
     * @param \Mageproxy\Connector\Api\Data\RecordingInterface $recording
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function delete(RecordingInterface $recording): void;
}
