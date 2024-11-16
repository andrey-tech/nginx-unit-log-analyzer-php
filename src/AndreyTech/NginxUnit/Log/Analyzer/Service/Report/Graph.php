<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Report;

use AndreyTech\NginxUnit\Log\Analyzer\Exception as AnalyzerException;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Builder;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Interval;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Parser\Processes;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Calculator;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Exporter;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Exporter\Duration\Average as AverageDurationExporter;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Exporter\Duration\Maximum as MaximalDurationExporter;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Exporter\Duration\Median as MedianDurationExporter;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Exporter\Duration\Minimum as MinimalDurationExporter;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Exporter\Quantity as QuantityExporter;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Plotter;
use DateTimeZone;
use Exception;
use Symfony\Component\Console\Helper\ProgressBar;

final class Graph extends Report
{
    /**
     * @throws Exception
     */
    public function create(Processes $processes): void
    {
        $plotter = new Plotter($this->console, $this->argv);
        $plotter->checkGnuplot();

        $exporterList = $this->makeExporterList();

        $applicationList = $processes->getApplicationList(true);

        $plotter->createCommandFile($applicationList, $exporterList);

        $reportTimezone = $this->argv->handleOptionReportTimezone();
        $builder = new Builder($reportTimezone);
        $totalInterval = $processes->getTimeInterval();

        $calculator = new Calculator();

        $maxProgressBar = count($applicationList) * $totalInterval->getHours();
        $progressBar = $this->console->makeProgressBar($maxProgressBar);

        $this->createHours(
            $processes,
            $builder,
            $calculator,
            $totalInterval,
            $progressBar,
            $reportTimezone,
            $applicationList,
            $exporterList
        );

        $progressBar->finish();
        $this->console->emptyLine();

        $plotter->plot();
    }

    /**
     * @param list<string> $applicationList
     * @param list<Exporter> $exporterList
     *
     * @throws Exception
     */
    private function createHours(
        Processes $processes,
        Builder $builder,
        Calculator $calculator,
        Interval $totalInterval,
        ProgressBar $progressBar,
        DateTimeZone $reportTimezone,
        array $applicationList,
        array $exporterList
    ): void {
        $hourIntervalIterator = $builder->buildHourIterator($totalInterval);

        foreach ($hourIntervalIterator as $hourInterval) {
            $metricList = [];

            foreach ($applicationList as $applicationName) {
                $processIterator = $processes->timeIntervalFilter($processes->getProcessIterator(), $hourInterval);
                $processIterator = $processes->applicationNameFilter($processIterator, $applicationName);

                $hourMetrics = $calculator->calculate($processIterator);
                $metricList[] = $hourMetrics;

                $progressBar->advance();
            }

            foreach ($exporterList as $exporter) {
                $exporter->export($metricList, $hourInterval, $reportTimezone);
            }
        }
    }

    /**
     * @return list<Exporter>
     */
    private function makeExporterList(): array
    {
        $graphTypes = $this->argv->handleOptionGraphTypes();

        $this->console->message(
            sprintf('Generate graph (types: %s)', implode(', ', $graphTypes))
        );

        $exporterList = [];

        foreach ($graphTypes as $graphType) {
            $exporterList[] = $this->makeExporter($graphType);
        }

        return $exporterList;
    }

    private function makeExporter(string $graphType): Exporter
    {
        switch ($graphType) {
            case 'quantity':
                return new QuantityExporter();
            case 'median':
                return new MedianDurationExporter();
            case 'average':
                return new AverageDurationExporter();
            case 'minimum':
                return new MinimalDurationExporter();
            case 'maximum':
                return new MaximalDurationExporter();
        }

        throw new AnalyzerException(
            sprintf('Unknown graph type "%s".', $graphType)
        );
    }
}
