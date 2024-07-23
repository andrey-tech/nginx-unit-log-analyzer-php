<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Top;

use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Renderer as ReportRenderer;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Top\Calculator\Metrics;
use DateTimeZone;
use Symfony\Component\Console\Helper\TableSeparator;

final class Renderer extends ReportRenderer
{
    public function addTableHeader(): void
    {
        $this->table->setHeaders([
            [ '#', 'Duration', 'App name', 'Start time', 'Exit time', 'Start', 'Exit', 'Id' ]
        ]);
    }

    public function addTableMinRows(Metrics $metrics, DateTimeZone $reportTimezone): void
    {
        $this->table->addRow([ new TableSeparator(['colspan' => 8]) ]);

        $metricList = $metrics->getMinMetricList();

        $rowNumber = count($metricList);

        foreach ($metricList as $metric) {
            $this->table->addRow([
                $rowNumber,
                $this->formatDuration($metric->getDuration()),
                $metric->getApplicationName(),
                $this->formatDateTime($metric->getStartTimestamp(), $reportTimezone),
                $this->formatDateTime($metric->getExitTimestamp(), $reportTimezone),
                $metric->getStartLine(),
                $metric->getExitLine(),
                $metric->getProcessId()
            ]);

            $rowNumber--;
        }
    }

    public function addTableMaxRows(Metrics $metrics, DateTimeZone $reportTimezone): void
    {
        $rowNumber = 0;
        foreach ($metrics->getMaxMetricList() as $metric) {
            $rowNumber++;
            $this->table->addRow([
                $rowNumber,
                $this->formatDuration($metric->getDuration()),
                $metric->getApplicationName(),
                $this->formatDateTime($metric->getStartTimestamp(), $reportTimezone),
                $this->formatDateTime($metric->getExitTimestamp(), $reportTimezone),
                $metric->getStartLine(),
                $metric->getExitLine(),
                $metric->getProcessId()
            ]);
        }
    }
}
