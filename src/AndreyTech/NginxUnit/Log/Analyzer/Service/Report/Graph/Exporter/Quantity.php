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

final class Quantity extends Exporter
{
    public function getName(): string
    {
        return 'Quantity of NGINX Unit processes within hour';
    }

    public function getParameterName(): string
    {
        return 'Quantity of processes';
    }

    public function getParameterFormat(): string
    {
        return '';
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
     * @return array<string|int>
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
     * @return int[]
     */
    private function buildMetricColumns(array $metricList): array
    {
        return array_map(
            static fn (Metric $metric): int => $metric->getTotalProcesses() - $metric->getNotExitedProcesses(),
            $metricList
        );
    }
}
