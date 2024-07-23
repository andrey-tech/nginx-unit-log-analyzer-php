<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service;

use AndreyTech\NginxUnit\Log\Analyzer\Argv\Filter;
use AndreyTech\NginxUnit\Log\Analyzer\Console;
use AndreyTech\NginxUnit\Log\Analyzer\Exception as AnalyzerException;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Parser\Processes;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Parser\Stats;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Generator;

final class Parser
{
    /**
     * Regex to parse line: 2024/06/19 12:58:50 [info] 36#36 "xxx" application started
     */
    private const START_REGEX = '|^(\d+/\d+/\d+ \d+:\d+:\d+) \[info\] (\d+)#\d+ "(.+?)" application started|';

    /**
     * Regex to parse line: 2024/06/19 12:59:08 [notice] 34#34 app process 36 exited with code 0
     */
    private const EXIT_REGEX = '|^(\d+/\d+/\d+ \d+:\d+:\d+) \[notice\] \d+#\d+ app process (\d+) exited|';

    private Processes $processes;
    private Stats $stats;

    public function __construct(
        private readonly Console $console,
    ) {
        $this->processes = new Processes();
        $this->stats = new Stats();
    }

    public function parse(string $logFile, Filter $logFilter, DateTimeZone $logTimezone): Processes
    {
        $this->console->message(
            sprintf('Parsing log file "%s" (TZ: %s)', $logFile, $logTimezone->getName())
        );

        $this->process($logFile, $logFilter, $logTimezone);

        $this->console->message((string) $this->stats);

        $this->console->message(
            sprintf('APP: %s', implode(', ', $this->processes->getApplicationList()))
        );

        return $this->processes;
    }

    private function process(string $logFile, Filter $logFilter, DateTimeZone $logTimezone): void
    {
        $progressBar = $this->console->makeProgressBar();

        foreach ($this->getLine($logFile) as $lineNumber => $lineContents) {
            $this->stats->lines++;
            $this->parseLine($lineNumber, $lineContents, $logFilter, $logTimezone);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->console->emptyLine();
    }

    private function parseLine(
        int $lineNumber,
        string $lineContents,
        Filter $logFilter,
        DateTimeZone $logTimezone
    ): void {
        $lineContents = trim($lineContents);

        if ($this->parseStartLine($lineNumber, $lineContents, $logFilter, $logTimezone)) {
            return;
        }

        $this->parseExitLine($lineNumber, $lineContents, $logTimezone);
    }

    private function parseStartLine(
        int $lineNumber,
        string $lineContents,
        Filter $logFilter,
        DateTimeZone $logTimezone
    ): bool {
        if (!preg_match(self::START_REGEX, $lineContents, $matches)) {
            return false;
        }

        $this->stats->start++;

        $startTime = $matches[1];
        $processId = (int) $matches[2];
        $applicationName = $matches[3];

        $startTimestamp = $this->parseTime($startTime, $lineContents, $logTimezone);

        if (!$this->filterStartLine($applicationName, $startTimestamp, $logFilter)) {
            return true;
        }

        $this->processes->addStart($lineNumber, $processId, $startTimestamp, $applicationName);
        $this->stats->startAdded++;

        return true;
    }

    private function parseExitLine(
        int $lineNumber,
        string $lineContents,
        DateTimeZone $logTimezone
    ): bool {
        if (!preg_match(self::EXIT_REGEX, $lineContents, $matches)) {
            return false;
        }

        $this->stats->exit++;

        $exitTime = $matches[1];
        $processId = (int) $matches[2];

        if (!$this->processes->hasNotExitedProcess($processId)) {
            $this->stats->exitSkipped++;

            return true;
        }

        $exitTimestamp = $this->parseTime($exitTime, $lineContents, $logTimezone);

        $this->processes->addExit($lineNumber, $processId, $exitTimestamp);
        $this->stats->exitAdded++;

        return true;
    }

    /**
     * @return Generator<int, string>
     */
    private function getLine(string $file): Generator
    {
        $handle = fopen($file, 'rb');

        if (false === $handle) {
            throw new AnalyzerException(
                sprintf('Can not open NGINX Unit log file "%s".', $file)
            );
        }

        $lineNumber = 0;

        while (!feof($handle)) {
            $lineContents = fgets($handle);
            $lineNumber++;
            if (false !== $lineContents) {
                yield $lineNumber => $lineContents;
            }
        }

        if (!fclose($handle)) {
            throw new AnalyzerException(
                sprintf('Can not close NGINX Unit log file "%s".', $file)
            );
        }
    }

    private function filterStartLine(string $applicationName, int $startTimestamp, Filter $logFilter): bool
    {
        $filterApplicationName = $logFilter->getApplicationName();

        if (null !== $filterApplicationName && $applicationName !== $filterApplicationName) {
            $this->stats->skippedByApplicationNameFilter++;

            return false;
        }

        $fromTimestamp = $logFilter->getFromTimestamp();

        if (null !== $fromTimestamp && $startTimestamp < $fromTimestamp) {
            $this->stats->skippedByFromTimestampFilter++;

            return false;
        }

        $toTimestamp = $logFilter->getToTimestamp();

        if (null !== $toTimestamp && $startTimestamp > $toTimestamp) {
            $this->stats->skippedByToTimestampFilter++;

            return false;
        }

        return true;
    }

    private function parseTime(string $time, string $line, DateTimeZone $logTimezone): int
    {
        try {
            return (new DateTimeImmutable($time, $logTimezone))->getTimestamp();
        } catch (Exception $exception) {
            throw new AnalyzerException(
                sprintf('Can not parse time in line "%s": %s.', $line, $exception->getMessage())
            );
        }
    }
}
