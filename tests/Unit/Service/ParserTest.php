<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace Test\Unit\Service;

use AndreyTech\NginxUnit\Log\Analyzer\Argv\Filter;
use AndreyTech\NginxUnit\Log\Analyzer\Console;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Parser;
use Test\Unit\TestCase;
use DateTimeZone;
use DateTimeImmutable;
use Exception;
use JsonException;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use RuntimeException;

final class ParserTest extends TestCase
{
    private const TEST_DIR = 'Service/Parser';
    private const LOG_FILE = 'unit.log';
    private const PROCESS_LIST_FILE = 'process_list.json';
    private const TIME_INTERVAL_FILE = 'time_interval.json';

    /**
     * @dataProvider parseDataProvider
     *
     * @param non-empty-string $timezone
     *
     * @throws JsonException
     * @throws Exception
     * @throws MockObjectException
     */
    public function testParse(
        int $testCaseNumber,
        string $timezone,
        ?string $from = null,
        ?string $to = null,
        ?string $applicationName = null
    ): void {
        $logFile = $this->getAbsoluteFileName(self::TEST_DIR, self::LOG_FILE, $testCaseNumber);
        $console = $this->buildConsole();
        $filter = $this->buildFilter($from, $to, $applicationName);
        $logTimezone = $this->buildLogTimezone($timezone);

        $processListFile = $this->getAbsoluteFileName(self::TEST_DIR, self::PROCESS_LIST_FILE, $testCaseNumber);
        $expectedProcessList = $this->loadJsonFile($processListFile);

        $expectedIntervalFile = $this->getAbsoluteFileName(self::TEST_DIR, self::TIME_INTERVAL_FILE, $testCaseNumber);
        $expectedInterval = $this->loadJsonFile($expectedIntervalFile);

        $parser = new Parser($console);
        $processes = $parser->parse($logFile, $filter, $logTimezone);

        $processListInterval = $processes->getTimeInterval();
        $processIterator = $processes->timeIntervalFilter($processes->getProcessIterator(), $processListInterval);
        $processList = iterator_to_array($processIterator);

        $this->assertSame(empty($expectedProcessList), $processes->isEmpty());
        $this->assertSame($expectedProcessList, $processList);

        $this->assertSame($expectedInterval['from'], $processListInterval->getFromTimestamp());
        $this->assertSame($expectedInterval['to'], $processListInterval->getToTimestamp());
    }

    // Data providers

    /**
     * @return iterable<string, array{0: int, 1: non-empty-string, 2?:string|null, 3?: string|null, 4?: string|null}>
     */
    public static function parseDataProvider(): iterable
    {
        yield 'No processes (log TZ: UTC)' => [ 0, 'UTC' ];
        yield 'One process (log TZ:UTC)' => [ 1, 'UTC' ];
        yield 'One process (log TZ:Europe/Moscow)' => [ 2, 'Europe/Moscow' ];
        yield 'Two processes, identical id (log TZ:UTC)' => [ 3, 'UTC' ];
        yield 'Two processes, entangled identical id (log TZ:UTC)' => [ 4, 'UTC' ];
        yield 'Two long processes (log TZ:UTC)' => [ 5, 'UTC' ];
        yield 'Two long processes (log TZ:Europe/Moscow)' => [ 6, 'Europe/Moscow' ];
        yield 'Not exited process (log TZ:UTC)' => [ 7, 'UTC' ];

        yield 'Filter from, TZ: UTC (log TZ:UTC)' => [ 8, 'UTC', '2024/06/13 15:20:00 UTC' ];
        yield 'Filter from, TZ: Europe/Moscow (log TZ:UTC)' => [ 8, 'UTC', '2024/06/13 18:20:00 Europe/Moscow' ];

        yield 'Filter to, TZ: UTC (log TZ:UTC)' => [ 9, 'UTC', null, '2024/06/13 14:00:00 UTC' ];
        yield 'Filter to, TZ: Europe/Moscow (log TZ:UTC)' => [ 10, 'UTC', null, '2024/06/13 17:00:00 Europe/Moscow' ];

        yield 'Filter application name (log TZ:UTC)' => [ 11, 'UTC', null, null, 'app2' ];
    }

    // Builders

    /**
     * @throws MockObjectException
     */
    private function buildConsole(): Console
    {
        return $this->createStub(Console::class);
    }

    /**
     * @throws Exception
     */
    private function buildFilter(?string $from = null, ?string $to = null, ?string $applicationName = null): Filter
    {
        $fromDatetime = null !== $from ? new DateTimeImmutable($from) : null;
        $toDatetime = null !== $to ? new DateTimeImmutable($to) : null;

        return new Filter($fromDatetime, $toDatetime, $applicationName);
    }

    /**
     * @param non-empty-string $timezone
     *
     * @throws Exception
     */
    private function buildLogTimezone(string $timezone): DateTimeZone
    {
        return new DateTimeZone($timezone);
    }
}
