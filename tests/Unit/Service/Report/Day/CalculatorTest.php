<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace Test\Unit\Service\Report\Day;

use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Day\Calculator;
use Test\Unit\TestCase;
use Generator;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Parser\Processes;
use JsonException;

/**
 * @psalm-import-type P from Processes
 */
final class CalculatorTest extends TestCase
{
    private const TEST_DIR = 'Service/Report/Day/Calculator';
    private const PROCESS_LIST_FILE = 'process_list.json';
    private const METRIC_FILE = 'metric.json';

    /**
     * @dataProvider calculateDataProvider
     *
     * @throws JsonException
     */
    public function testCalculate(int $testCaseNumber): void
    {
        $processIterator = $this->buildProcessIterator($testCaseNumber);

        $metricFile = $this->getAbsoluteFileName(self::TEST_DIR, self::METRIC_FILE, $testCaseNumber);
        $expectedMetric = $this->loadJsonFile($metricFile);

        $calculator = new Calculator();
        $metric = $calculator->calculate($processIterator);

        $this->assertSame($expectedMetric['totalProcesses'], $metric->getTotalProcesses());
        $this->assertSame($expectedMetric['notExitedProcesses'], $metric->getNotExitedProcesses());
        $this->assertSame($expectedMetric['applicationList'], $metric->getApplicationList());
        $this->assertSame($expectedMetric['minDuration'], $metric->getMinDuration());
        $this->assertSame($expectedMetric['maxDuration'], $metric->getMaxDuration());
        $this->assertSame($expectedMetric['averageDuration'], $metric->getAverageDuration());
        $this->assertSame($expectedMetric['medianDuration'], $metric->getMedianDuration());
        $this->assertSame($expectedMetric['stdDuration'], $metric->getStdDuration());
    }

    // Data providers

    /**
     * @return iterable<string, array{int}>
     */
    public static function calculateDataProvider(): iterable
    {
        yield 'Zero process quantity' => [ 0 ];
        yield 'Odd process quantity' => [ 1 ];
        yield 'Even process quantity' => [ 2 ];
    }

    // Builders

    /**
     * @psalm-return Generator<P>
     *
     * @throws JsonException
     */
    private function buildProcessIterator(int $testCaseNumber): Generator
    {
        $processListFile = $this->getAbsoluteFileName(self::TEST_DIR, self::PROCESS_LIST_FILE, $testCaseNumber);
        $processList = $this->loadJsonFile($processListFile);

        /** @psalm-var P $process */
        foreach ($processList as $process) {
            yield $process;
        }
    }


}
