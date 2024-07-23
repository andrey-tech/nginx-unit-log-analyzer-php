<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace Test\Unit\Service;

use AndreyTech\NginxUnit\Log\Analyzer\Service\Builder;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Interval;
use DateTimeZone;
use Exception;
use Test\Unit\TestCase;

final class BuilderTest extends TestCase
{
    /**
     * @dataProvider buildDayIteratorDataProvider
     *
     * @param array{int, int} $fromTo
     * @param non-empty-string $timezone
     * @param list<array{int, int}> $expectedIntervals
     *
     * @throws Exception
     */
    public function testBuildDayIterator(
        array $fromTo,
        string $timezone,
        array $expectedIntervals
    ): void {
        $reportTimezone = $this->buildReportTimezone($timezone);
        $interval = $this->buildInterval($fromTo);

        $builder = new Builder($reportTimezone);
        $dayIntervalIterator = $builder->buildDayIterator($interval);

        $index = 0;
        foreach ($dayIntervalIterator as $dayInterval) {
            $this->assertSame($expectedIntervals[$index][0], $dayInterval->getFromTimestamp());
            $this->assertSame($expectedIntervals[$index][1], $dayInterval->getToTimestamp());
            $index++;
        }
    }

    /**
     * @dataProvider buildHourIteratorDataProvider
     *
     * @param array{int, int} $fromTo
     * @param non-empty-string $timezone
     * @param list<array{int, int}> $expectedIntervals
     *
     * @throws Exception
     */
    public function testBuildHourIterator(
        array $fromTo,
        string $timezone,
        array $expectedIntervals
    ): void {
        $reportTimezone = $this->buildReportTimezone($timezone);
        $interval = $this->buildInterval($fromTo);

        $builder = new Builder($reportTimezone);
        $hourIntervalIterator = $builder->buildHourIterator($interval);

        foreach ($expectedIntervals as $expectedInterval) {
            $hourInterval = $hourIntervalIterator->current();
            $this->assertSame($expectedInterval[0], $hourInterval->getFromTimestamp());
            $this->assertSame($expectedInterval[1], $hourInterval->getToTimestamp());
            $hourIntervalIterator->next();
        }
    }

    // Data providers

    /**
     * @return iterable<
     *     string,
     *     array{
     *         array{int, int},
     *         non-empty-string,
     *         list<array{int, int}>
     *     }
     * >
     */
    public static function buildDayIteratorDataProvider(): iterable
    {
        yield 'Evening time (TZ: UTC)' => [
            [ 1718317374, 1718569374 ],
            'UTC',
            [
                [ 1718236800, 1718323199 ],
                [ 1718323200, 1718409599 ],
                [ 1718409600, 1718495999 ],
                [ 1718496000, 1718582399 ],
            ]
        ];

        yield 'Evening time (TZ: Europe/Moscow)' => [
            [ 1718317374, 1718569374 ],
            'Europe/Moscow',
            [
                [ 1718312400, 1718398799 ],
                [ 1718398800, 1718485199 ],
                [ 1718485200, 1718571599 ],
            ]
        ];

        yield 'Morning time (TZ: UTC)' => [
            [ 1718285466, 1718569374 ],
            'UTC',
            [
                [ 1718236800, 1718323199 ],
                [ 1718323200, 1718409599 ],
                [ 1718409600, 1718495999 ],
                [ 1718496000, 1718582399 ],
            ]
        ];

        yield 'Morning time (TZ: Europe/Moscow)' => [
            [ 1718285466, 1718569374 ],
            'Europe/Moscow',
            [
                [ 1718226000, 1718312399 ],
                [ 1718312400, 1718398799 ],
                [ 1718398800, 1718485199 ],
                [ 1718485200, 1718571599 ],
            ]
        ];
    }

    /**
     * @return iterable<
     *     string,
     *     array{
     *         array{int, int},
     *         non-empty-string,
     *         list<array{int, int}>
     *     }
     * >
     */
    public static function buildHourIteratorDataProvider(): iterable
    {
        yield 'Evening time (TZ: UTC)' => [
            [ 1718317374, 1718569374 ],
            'UTC',
            [
                [ 1718316000, 1718319599 ],
                [ 1718319600, 1718323199 ],
                [ 1718323200, 1718326799 ],
                [ 1718326800, 1718330399 ],
            ]
        ];

        yield 'Evening time (TZ: Europe/Moscow)' => [
            [ 1718317374, 1718569374 ],
            'Europe/Moscow',
            [
                [ 1718316000, 1718319599 ],
                [ 1718319600, 1718323199 ],
                [ 1718323200, 1718326799 ],
                [ 1718326800, 1718330399 ],
            ]
        ];

        yield 'Morning time (TZ: UTC)' => [
            [ 1718285466, 1718569374 ],
            'UTC',
            [
                [ 1718283600, 1718287199 ],
                [ 1718287200, 1718290799 ],
                [ 1718290800, 1718294399 ],
                [ 1718294400, 1718297999 ],
            ]
        ];

        yield 'Morning time (TZ: Europe/Moscow)' => [
            [ 1718285466, 1718569374 ],
            'Europe/Moscow',
            [
                [ 1718283600, 1718287199 ],
                [ 1718287200, 1718290799 ],
                [ 1718290800, 1718294399 ],
                [ 1718294400, 1718297999 ],
            ]
        ];
    }

    // Builders

    /**
     * @param array{int, int} $fromTo
     */
    private function buildInterval(array $fromTo): Interval
    {
        return new Interval($fromTo[0], $fromTo[1]);
    }

    /**
     * @param non-empty-string $timezone
     *
     * @throws Exception
     */
    private function buildReportTimezone(string $timezone): DateTimeZone
    {
        return new DateTimeZone($timezone);
    }
}
