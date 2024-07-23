<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Day;

use AndreyTech\NginxUnit\Log\Analyzer\Colorizer;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Interval;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Day\Calculator\Metric;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Renderer as ReportRenderer;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;

final class Renderer extends ReportRenderer
{
    public function addTableHeader(): void
    {
        $this->table->setHeaders([
            [
                new TableCell('Processes', [ 'colspan' => 2 ]),
                new TableCell('Duration', [ 'colspan' => 5 ])
            ],
            [ 'Start', 'Amount', 'Median', 'Average', 'Std dev', 'Min', 'Max' ]
        ]);
    }

    /**
     * @throws Exception
     */
    public function addTableRow(Metric $metrics, Interval $dayInterval, DateTimeZone $reportTimezone): void
    {
        $this->table->addRow([
            $this->formatTimeInterval($dayInterval, $reportTimezone),
            $this->formatProcessCounter($metrics->getTotalProcesses(), $metrics->getNotExitedProcesses()),
            $this->formatDuration($metrics->getMedianDuration()),
            $this->formatDuration($metrics->getAverageDuration()),
            $this->formatDuration($metrics->getStdDuration()),
            $this->formatDuration($metrics->getMinDuration()),
            $this->formatDuration($metrics->getMaxDuration()),
        ]);
    }

    public function addTableFooter(Metric $metrics): void
    {
        $this->table->addRows([
            new TableSeparator(),
            [
                Colorizer::whiteBold('00-24'),
                $this->formatProcessCounter($metrics->getTotalProcesses(), $metrics->getNotExitedProcesses()),
                $this->formatDuration($metrics->getMedianDuration()),
                $this->formatDuration($metrics->getAverageDuration()),
                $this->formatDuration($metrics->getStdDuration()),
                $this->formatDuration($metrics->getMinDuration()),
                $this->formatDuration($metrics->getMaxDuration()),
            ]
        ]);
    }

    public function addTotalTableFooter(Metric $metrics): void
    {
        $this->table->addRow([
            Colorizer::whiteBold('Total'),
            $this->formatProcessCounter($metrics->getTotalProcesses(), $metrics->getNotExitedProcesses()),
            $this->formatDuration($metrics->getMedianDuration()),
            $this->formatDuration($metrics->getAverageDuration()),
            $this->formatDuration($metrics->getStdDuration()),
            $this->formatDuration($metrics->getMinDuration()),
            $this->formatDuration($metrics->getMaxDuration()),
        ]);
    }

    private function formatProcessCounter(int $total, int $notExited): string
    {
        $value = (string) $total;

        if ($notExited > 0) {
            $value .= sprintf('+%u', $notExited);
        }

        return $value;
    }

    /**
     * @throws Exception
     */
    private function formatTimeInterval(Interval $interval, DateTimeZone $reportTimezone): string
    {
        $from = (new DateTimeImmutable('now', $reportTimezone))
            ->setTimestamp($interval->getFromTimestamp())
            ->format('H');

        $to = (new DateTimeImmutable('now', $reportTimezone))
            ->setTimestamp($interval->getToTimestamp() + 1)
            ->format('H');

        if ('00' === $to) {
            $to = '24';
        }

        return Colorizer::whiteBold(sprintf('%s-%s', $from, $to));
    }
}
