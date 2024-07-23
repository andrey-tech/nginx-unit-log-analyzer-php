<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Day\Calculator;

final class Metric
{
    private int $totalProcesses = 0;
    private int $notExitedProcesses = 0;

    /**
     * @var list<string>
     */
    private array $applicationList = [];

    private ?int $minDuration = null;
    private ?int $maxDuration = null;
    private ?int $averageDuration = null;
    private ?int $medianDuration = null;
    private ?int $stdDuration = null;

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
        $this->stdDuration = (int) round($this->calculateStdDeviation($durationList, $averageDuration));
    }

    public function addApplication(string $name): void
    {
        if (!in_array($name, $this->applicationList, true)) {
            $this->applicationList[] = $name;
        }
    }

    public function getTotalProcesses(): int
    {
        return $this->totalProcesses;
    }

    public function getNotExitedProcesses(): int
    {
        return $this->notExitedProcesses;
    }

    /**
     * @return list<string>
     */
    public function getApplicationList(): array
    {
        sort($this->applicationList, SORT_STRING);

        return $this->applicationList;
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

    public function getStdDuration(): ?int
    {
        return $this->stdDuration;
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

    /**
     * @param non-empty-array<int> $values
     *
     * @psalm-pure
     */
    private function calculateStdDeviation(array $values, float $average): float
    {
        $variance = array_reduce(
            $values,
            static fn (float $std, int $value): float => $std + ($value - $average) ** 2,
            0.0
        );

        return sqrt($variance / count($values));
    }
}
