<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace Test\Unit\Service\Report\Renderer;

use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Renderer\Helper;
use Test\Unit\TestCase;

final class HelperTest extends TestCase
{
    /**
     * @dataProvider secondsToTimeDataProvider
     */
    public function testSecondsToTime(int $seconds, string $expected): void
    {
        $this->assertSame($expected, Helper::secondsToTime($seconds));
    }

    /**
     * @return iterable<string, array{int, string}>
     */
    public static function secondsToTimeDataProvider(): iterable
    {
        yield '-1' => [ -1, '-1s' ];
        yield '0' => [ 0, '0s' ];
        yield '1' => [ 1, '1s' ];
        yield '59' => [ 59, '59s' ];
        yield '60' => [ 60, '1m' ];
        yield '61' => [ 61, '1m 1s' ];
        yield '317' => [ 317, '5m 17s' ];
        yield '3599' => [ 3599, '59m 59s' ];
        yield '3600' => [ 3600, '1h' ];
        yield '3602' => [ 3602, '1h 2s' ];
        yield '3900' => [ 3900, '1h 5m' ];
        yield '3907' => [ 3907, '1h 5m 7s' ];
        yield '86399' => [ 86399, '23h 59m 59s' ];
        yield '86400' => [ 86400, '1d' ];
        yield '86401' => [ 86401, '1d 1s' ];
        yield '86501' => [ 86501, '1d 1m 41s' ];
        yield '99517' => [ 99517, '1d 3h 38m 37s' ];
    }
}
