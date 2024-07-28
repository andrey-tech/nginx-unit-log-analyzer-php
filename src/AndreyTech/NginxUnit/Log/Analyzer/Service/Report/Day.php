<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Report;

use AndreyTech\NginxUnit\Log\Analyzer\Service\Builder;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Interval;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Parser\Processes;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Day\Calculator;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Day\Renderer;
use DateTimeZone;
use Exception;

final class Day extends Report
{
    /**
     * @throws Exception
     */
    public function create(Processes $processes): void
    {
        $reportTimezone = $this->argv->handleOptionReportTimezone();

        $this->createDays($processes, $reportTimezone);

        $this->createTotal($processes, $reportTimezone);
    }

    /**
     * @throws Exception
     */
    private function createDays(Processes $processes, DateTimeZone $reportTimezone): void
    {
        $builder = new Builder($reportTimezone);
        $totalInterval = $processes->getTimeInterval();
        $dayIntervalIterator = $builder->buildDayIterator($totalInterval);

        $calculator = new Calculator();

        foreach ($dayIntervalIterator as $dayInterval) {
            $renderer = new Renderer($this->console);
            $renderer->renderTitleDate($dayInterval, $reportTimezone);

            $processIterator = $processes->timeIntervalFilter($processes->getProcessIterator(), $dayInterval);
            $dayMetrics = $calculator->calculate($processIterator);

            $applicationList = $dayMetrics->getApplicationList();
            $renderer->renderTitleApplicationList($applicationList);
            $renderer->addTableHeader();

            $this->createHours($processes, $builder, $calculator, $renderer, $dayInterval, $reportTimezone);
            $renderer->addTableFooter($dayMetrics);

            $renderer->renderTable();
        }
    }

    /**
     * @throws Exception
     */
    private function createHours(
        Processes $processes,
        Builder $builder,
        Calculator $calculator,
        Renderer $renderer,
        Interval $dayInterval,
        DateTimeZone $reportTimezone
    ): void {
        $hourIntervalIterator = $builder->buildHourIterator($dayInterval);

        foreach ($hourIntervalIterator as $hourInterval) {
            $processIterator = $processes->timeIntervalFilter($processes->getProcessIterator(), $hourInterval);
            $hourMetrics = $calculator->calculate($processIterator);
            $renderer->addTableRow($hourMetrics, $hourInterval, $reportTimezone);
        }
    }

    /**
     * @throws Exception
     */
    private function createTotal(Processes $processes, DateTimeZone $reportTimezone): void
    {
        $totalInterval = $processes->getTimeInterval();
        $processIterator = $processes->timeIntervalFilter($processes->getProcessIterator(), $totalInterval);

        $renderer = new Renderer($this->console);
        $renderer->renderTotalTitleDate($totalInterval, $reportTimezone);

        $calculator = new Calculator();
        $totalMetrics = $calculator->calculate($processIterator);

        $applicationList = $totalMetrics->getApplicationList();
        $renderer->renderTitleApplicationList($applicationList);

        $renderer->addTableHeader();
        $renderer->addTotalTableFooter($totalMetrics);

        $renderer->renderTable();
    }
}
