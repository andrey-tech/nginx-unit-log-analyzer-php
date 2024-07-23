<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Exporter;

use AndreyTech\NginxUnit\Log\Analyzer\Service\Interval;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Calculator\Metric;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Exporter;
use DateTimeZone;
use Exception;

abstract class Duration extends Exporter
{
    abstract protected function getDuration(Metric $metric): ?int;

    public function getParameterName(): string
    {
        return 'Duration of processes, H:MM::SS';
    }

    public function getParameterFormat(): string
    {
        return '%tH:%tM:%tS';
    }

    /**
     * @param list<Metric> $metricList
     *
     * @throws Exception
     */
    public function export(array $metricList, Interval $dayInterval, DateTimeZone $reportTimezone): void
    {
        $row = $this->map($metricList, $dayInterval, $reportTimezone);

        $this->save($row);
    }

    /**
     * @param list<Metric> $metricList
     *
     * @return list<string|int>
     *
     * @throws Exception
     */
    private function map(array $metricList, Interval $dayInterval, DateTimeZone $reportTimezone): array
    {
        $row = [];
        $row[] = $this->formatDateTime($dayInterval->getToTimestamp(), $reportTimezone);

        $metricColumns = $this->buildMetricColumns($metricList);

        return array_merge($row, $metricColumns);
    }

    /**
     * @param list<Metric> $metricList
     *
     * @return list<int>
     */
    private function buildMetricColumns(array $metricList): array
    {
        return array_map(
            fn (Metric $metric): int => (int) $this->getDuration($metric),
            $metricList
        );
    }
}
