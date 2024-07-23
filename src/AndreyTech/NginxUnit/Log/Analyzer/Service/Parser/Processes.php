<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Parser;

use AndreyTech\NginxUnit\Log\Analyzer\Service\Interval;
use Generator;
use LogicException;

/**
 * @psalm-type P = array{
 *     id: int,
 *     name: string,
 *     start: int,
 *     startLine: int,
 *     exit: int|null,
 *     exitLine: int|null,
 *     duration: int|null
 * }
 * @psalm-type PL = array<int, array<int, P>>
 */
final class Processes
{
    /**
     * @psalm-var PL
     */
    private array $processList = [];

    public function hasNotExitedProcess(int $processId): bool
    {
        return null !== $this->getNotExitedProcessData($processId);
    }

    public function addStart(int $lineNumber, int $processId, int $startTimestamp, string $applicationName): void
    {
        $this->processList[$processId][] = [
            'id' => $processId,
            'name' => $applicationName,
            'start' => $startTimestamp,
            'startLine' => $lineNumber,
            'exit' => null,
            'exitLine' => null,
            'duration' => null,
        ];
    }

    public function addExit(int $lineNumber, int $processId, int $exitTimestamp): void
    {
        $processData = $this->getNotExitedProcessData($processId);

        if (null === $processData) {
            throw new LogicException(
                sprintf('Unknown process id "%u" in line "%u".', $processId, $lineNumber)
            );
        }

        [ $processNumber, $process ] = $processData;

        $startTimestamp = $process['start'];
        $process['exitLine'] = $lineNumber;
        $process['exit'] = $exitTimestamp;
        $process['duration'] = $exitTimestamp - $startTimestamp;

        $this->processList[$processId][$processNumber] = $process;
    }

    public function getTimeInterval(): Interval
    {
        $minTimestamp = null;
        $maxTimestamp = null;

        foreach ($this->processList as $processes) {
            foreach ($processes as $process) {
                if (null === $minTimestamp || $process['start'] < $minTimestamp) {
                    $minTimestamp = $process['start'];
                }

                if (null === $maxTimestamp || $process['start'] > $maxTimestamp) {
                    $maxTimestamp = $process['start'];
                }
            }
        }

        if (null === $minTimestamp || null === $maxTimestamp) {
            return new Interval(0, 0);
        }

        return new Interval($minTimestamp, $maxTimestamp);
    }

    public function isEmpty(): bool
    {
        return empty($this->processList);
    }

    /**
     * @psalm-return Generator<P>
     */
    public function getProcessIterator(): Generator
    {
        foreach ($this->processList as $processes) {
            foreach ($processes as $process) {
                yield $process;
            }
        }
    }

    /**
     * @psalm-param Generator<P> $processIterator
     *
     * @psalm-return Generator<P>
     */
    public function timeIntervalFilter(Generator $processIterator, Interval $interval): Generator
    {
        foreach ($processIterator as $process) {
            $startTimestamp = $process['start'];

            if ($startTimestamp < $interval->getFromTimestamp() || $startTimestamp > $interval->getToTimestamp()) {
                continue;
            }

            yield $process;
        }
    }

    /**
     * @psalm-param Generator<P> $processIterator
     *
     * @psalm-return Generator<P>
     */
    public function applicationNameFilter(iterable $processIterator, string $applicationName): Generator
    {
        foreach ($processIterator as $process) {
            if ($process['name'] !== $applicationName) {
                continue;
            }

            yield $process;
        }
    }

    /**
     * @return list<string>
     */
    public function getApplicationList(bool $onlyExitedProcesses = false): array
    {
        $applicationList = [];

        foreach ($this->getProcessIterator() as $process) {
            if ($onlyExitedProcesses && null === $process['exit']) {
                continue;
            }

            if (!in_array($process['name'], $applicationList, true)) {
                $applicationList[] = $process['name'];
            }
        }

        sort($applicationList, SORT_STRING);

        return $applicationList;
    }

    /**
     * @psalm-return array{int, P}|null
     */
    private function getNotExitedProcessData(int $processId): ?array
    {
        $processes = $this->processList[$processId] ?? [];

        if (empty($processes)) {
            return null;
        }

        foreach ($processes as $number => $process) {
            if (null === $process['exit']) {
                return [ $number, $process ];
            }
        }

        return null;
    }
}
