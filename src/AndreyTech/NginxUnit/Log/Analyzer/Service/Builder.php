<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Generator;

final class Builder
{
    private const SECONDS_IN_HOUR = 60 * 60;
    private const SECONDS_IN_DAY = 24 * self::SECONDS_IN_HOUR;

    public function __construct(
        private readonly DateTimeZone $reportTimezone
    ) {
    }

    /**
     * @return Generator<Interval>
     *
     * @throws Exception
     */
    public function buildDayIterator(Interval $interval): Generator
    {
        $timestamp = $interval->getFromTimestamp();
        $maxTimestamp = $interval->getToTimestamp();

        while (true) {
            $dayInterval = $this->getDayInterval($timestamp);

            yield $dayInterval;

            $timestamp = $dayInterval->getFromTimestamp() + self::SECONDS_IN_DAY;

            if ($timestamp > $maxTimestamp) {
                break;
            }
        }
    }

    /**
     * @return Generator<Interval>
     *
     * @throws Exception
     */
    public function buildHourIterator(Interval $interval): Generator
    {
        $timestamp = $interval->getFromTimestamp();
        $maxTimestamp = $interval->getToTimestamp();

        while (true) {
            $hourInterval = $this->getHourInterval($timestamp);

            yield $hourInterval;

            $timestamp = $hourInterval->getFromTimestamp() + self::SECONDS_IN_HOUR;

            if ($timestamp > $maxTimestamp) {
                break;
            }
        }
    }

    /**
     * @throws Exception
     */
    private function getDayInterval(int $timestamp): Interval
    {
        $now = new DateTimeImmutable('now', $this->reportTimezone);
        $datetime = $now->setTimestamp($timestamp);

        $beginOfDay = $datetime->modify('today');
        $endOfDay = $beginOfDay->modify('tomorrow -1 second');

        return new Interval(
            $beginOfDay->getTimestamp(),
            $endOfDay->getTimestamp()
        );
    }

    /**
     * @throws Exception
     */
    private function getHourInterval(int $timestamp): Interval
    {
        $now = new DateTimeImmutable('now', $this->reportTimezone);
        $datetime = $now->setTimestamp($timestamp);

        $hour = (int) $datetime->format('G');
        $beginOfHour = $datetime->setTime($hour, 0);
        $endOfHour = $datetime->setTime($hour, 59, 59);

        return new Interval(
            $beginOfHour->getTimestamp(),
            $endOfHour->getTimestamp()
        );
    }
}
