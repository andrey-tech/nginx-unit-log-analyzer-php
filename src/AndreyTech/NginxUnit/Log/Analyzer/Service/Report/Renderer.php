<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Report;

use AndreyTech\NginxUnit\Log\Analyzer\Colorizer;
use AndreyTech\NginxUnit\Log\Analyzer\Console;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Interval;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Renderer\Helper;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;

abstract class Renderer
{
    public const SYMBOL_DASH = '—';

    protected readonly Table $table;

    public function __construct(
        protected readonly Console $console,
    ) {
        $this->table = $this->buildTable();
    }

    /**
     * @throws Exception
     */
    public function renderTitleDate(Interval $interval, DateTimeZone $reportTimezone): void
    {
        $this->console->emptyLine();

        $this->console->message(
            Colorizer::yellow(
                sprintf(
                    'DATE: %s (TZ: %s)',
                    $this->formatDate($interval->getFromTimestamp(), $reportTimezone),
                    $reportTimezone->getName()
                )
            )
        );
    }

    /**
     * @throws Exception
     */
    public function renderTotalTitleDate(Interval $interval, DateTimeZone $reportTimezone): void
    {
        $this->console->emptyLine();

        $this->console->message(
            Colorizer::yellow(
                sprintf(
                    'DATE: %s %s %s (TZ: %s)',
                    $this->formatDateTime($interval->getFromTimestamp(), $reportTimezone),
                    self::SYMBOL_DASH,
                    $this->formatDateTime($interval->getToTimestamp(), $reportTimezone),
                    $reportTimezone->getName()
                )
            )
        );
    }

    /**
     * @param list<string> $applicationList
     */
    public function renderTitleApplicationList(array $applicationList): void
    {
        $this->console->message(
            Colorizer::yellow(
                sprintf('APP: %s', $this->formatApplicationList($applicationList))
            )
        );
    }

    public function renderTable(): void
    {
        $this->table->render();
    }

    protected function formatDuration(?int $seconds): string
    {
        if (null === $seconds) {
            return self::SYMBOL_DASH;
        }

        return Helper::secondsToTime($seconds);
    }

    /**
     * @throws Exception
     */
    protected function formatDateTime(int $timestamp, DateTimeZone $reportTimezone): string
    {
        return (new DateTimeImmutable('now', $reportTimezone))->setTimestamp($timestamp)->format('Y-m-d H:i:s');
    }

    /**
     * @throws Exception
     */
    private function formatDate(int $timestamp, DateTimeZone $reportTimezone): string
    {
        return (new DateTimeImmutable('now', $reportTimezone))->setTimestamp($timestamp)->format('Y-m-d');
    }

    /**
     * @param list<string> $applicationList
     */
    private function formatApplicationList(array $applicationList): string
    {
        return implode(', ', $applicationList);
    }

    private function buildTable(): Table
    {
        $style = new TableStyle();
        $style->setCellHeaderFormat('<fg=white;bg=default;options=bold>%s</>');

        $table = new Table($this->console->getConsoleOutput());
        $table->setStyle($style);

        return $table;
    }
}
