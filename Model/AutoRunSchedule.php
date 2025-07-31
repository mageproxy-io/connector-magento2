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

class AutoRunSchedule
{
    private array $schedule;

    private const RECORD_FOR = 'record_for';
    private const RECORD_TIME_UNIT = 'record_time_unit';
    private const PAUSE_FOR = 'pause_for';
    private const PAUSE_TIME_UNIT = 'record_time_unit';

    public const SECONDS = 's';
    public const MINUTES = 'm';
    public const HOURS = 'h';
    public const DAYS = 'd';

    public function __construct(
        array $schedule
    ) {
        $this->schedule = $schedule;
    }

    /**
     * Returns the required recording duration in minutes by iteration
     *
     * @param int $iteration
     * @return int
     */
    public function getRecordForDuration(int $iteration, string $unit = self::MINUTES): int
    {
        return $this->getDuration($iteration, self::RECORD_FOR, self::RECORD_TIME_UNIT, $unit);
    }

    public function getPauseForDuration(int $iteration, string $unit = self::MINUTES): int
    {
        return $this->getDuration($iteration, self::PAUSE_FOR, self::PAUSE_TIME_UNIT, $unit);
    }

    public function getIterations(): int
    {
        return count($this->schedule);
    }

    private function getDuration(int $iteration, $type, $unitType, string $toUnit = self::MINUTES): int
    {
        $iteration--; // Iteration is 1-based
        $duration = (int) $this->schedule[$iteration][$type];
        $fromUnit = $this->schedule[$iteration][$unitType];
        return $this->convert($duration, $fromUnit, $toUnit);
    }

    private function convert($time, $fromUnit, $toUnit): int
    {

        // Define conversion factors
        $conversion = [
            self::SECONDS => 1,
            self::MINUTES => 60,
            self::HOURS   => 3600,
            self::DAYS    => 86400,
        ];

        // Check if units are valid
        if (!isset($conversion[$fromUnit]) || !isset($conversion[$toUnit])) {
            throw new \RuntimeException('Invalid time units provided.');
        }

        // Convert to seconds, then to target unit
        $timeInSeconds = $time * $conversion[$fromUnit];
        return (int) floor($timeInSeconds / $conversion[$toUnit]);
    }

}
