<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Calculator;

final class Metric
{
    private int $totalProcesses = 0;
    private int $notExitedProcesses = 0;

    private ?int $minDuration = null;
    private ?int $maxDuration = null;

    private ?int $averageDuration = null;
    private ?int $medianDuration = null;

    public function incrementTotalProcesses(): void
    {
        $this->totalProcesses++;
    }

    public function incrementNotExitedProcesses(): void
    {
        $this->notExitedProcesses++;
    }

    public function updateDuration(?int $duration): void
    {
        if (null === $this->minDuration || $duration < $this->minDuration) {
            $this->minDuration = $duration;
        }

        if (null === $this->maxDuration || $duration > $this->maxDuration) {
            $this->maxDuration = $duration;
        }
    }

    /**
     * @param list<int> $durationList
     */
    public function updateTotal(array $durationList): void
    {
        if (empty($durationList)) {
            return;
        }

        $totalDurations = count($durationList);

        $averageDuration = array_sum($durationList) / $totalDurations;
        $this->averageDuration = (int) round($averageDuration);

        $this->medianDuration = (int) round($this->calculateMedian($durationList));
    }

    public function getTotalProcesses(): int
    {
        return $this->totalProcesses;
    }

    public function getNotExitedProcesses(): int
    {
        return $this->notExitedProcesses;
    }

    public function getMinDuration(): ?int
    {
        return $this->minDuration;
    }

    public function getMaxDuration(): ?int
    {
        return $this->maxDuration;
    }

    public function getAverageDuration(): ?int
    {
        return $this->averageDuration;
    }

    public function getMedianDuration(): ?int
    {
        return $this->medianDuration;
    }

    /**
     * @param non-empty-array<int> $values
     *
     * @psalm-pure
     */
    private function calculateMedian(array $values): float
    {
        sort($values, SORT_NUMERIC);

        $middleIndex = count($values) / 2;

        if (is_float($middleIndex)) {
            return $values[(int) $middleIndex];
        }

        return ($values[$middleIndex] + $values[$middleIndex - 1]) / 2;
    }
}
