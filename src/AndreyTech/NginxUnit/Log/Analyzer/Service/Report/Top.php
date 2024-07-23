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
use AndreyTech\NginxUnit\Log\Analyzer\Service\Parser\Processes;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Top\Calculator;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Top\Renderer;
use DateTimeZone;

final class Top extends Report
{
    public function create(Processes $processes): void
    {
        $reportTimezone = $this->argv->handleOptionReportTimezone();

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

            $renderer->addTableMaxRows($dayMetrics, $reportTimezone);
            $renderer->addTableMinRows($dayMetrics, $reportTimezone);

            $renderer->renderTable();
        }

        $this->createTotal($processes, $reportTimezone);
    }

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
        $renderer->addTableMaxRows($totalMetrics, $reportTimezone);
        $renderer->addTableMinRows($totalMetrics, $reportTimezone);

        $renderer->renderTable();
    }
}
