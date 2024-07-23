<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Top\Calculator;

final class Metrics
{
    /**
     * @var list<Metric>
     */
    private array $maxMetrics = [];

    /**
     * @var list<Metric>
     */
    private array $minMetricList = [];

    /**
     * @var list<string>
     */
    private array $applicationList = [];

    public function __construct(
        private readonly int $minMetricListSize = 5,
        private readonly int $maxMetricListSize = 20
    ) {
    }

    public function addProcess(
        int $processId,
        string $applicationName,
        int $startTimestamp,
        int $startLine,
        ?int $exitTimestamp,
        ?int $exitLine,
        ?int $duration
    ): void {
        if (null === $exitTimestamp || null === $exitLine || null === $duration) {
            return;
        }

        $metric = new Metric(
            $processId,
            $applicationName,
            $startTimestamp,
            $startLine,
            $exitTimestamp,
            $exitLine,
            $duration
        );

        $this->updateMinMetricList($metric);
        $this->updateMaxMetricList($metric);
        $this->addApplication($applicationName);
    }

    private function updateMinMetricList(Metric $metric): void
    {
        $this->minMetricList[] = $metric;

        usort(
            $this->minMetricList,
            static fn (Metric $metric1, Metric $metric2): int => $metric2->getDuration() <=> $metric1->getDuration()
        );

        $this->minMetricList = array_slice($this->minMetricList, - $this->minMetricListSize);
    }

    private function updateMaxMetricList(Metric $metric): void
    {
        $this->maxMetrics[] = $metric;

        usort(
            $this->maxMetrics,
            static fn (Metric $metric1, Metric $metric2): int => $metric2->getDuration() <=> $metric1->getDuration()
        );

        $this->maxMetrics = array_slice($this->maxMetrics, 0, $this->maxMetricListSize);
    }

    public function addApplication(string $name): void
    {
        if (!in_array($name, $this->applicationList, true)) {
            $this->applicationList[] = $name;
        }
    }

    /**
     * @return list<string>
     */
    public function getApplicationList(): array
    {
        sort($this->applicationList, SORT_STRING);

        return $this->applicationList;
    }

    /**
     * @return list<Metric>
     */
    public function getMinMetricList(): array
    {
        return $this->minMetricList;
    }

    /**
     * @return list<Metric>
     */
    public function getMaxMetricList(): array
    {
        return $this->maxMetrics;
    }
}
