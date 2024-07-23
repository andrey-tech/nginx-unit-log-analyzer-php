<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Day;

use AndreyTech\NginxUnit\Log\Analyzer\Service\Parser\Processes;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Day\Calculator\Metric;
use Generator;

/**
 * @psalm-import-type P from Processes
 */
final class Calculator
{
    /**
     * @psalm-param Generator<P> $processIterator
     */
    public function calculate(Generator $processIterator): Metric
    {
        $metrics = new Metric();

        /** @var list<int> $durationList */
        $durationList = [];

        foreach ($processIterator as $process) {
            $metrics->incrementTotalProcesses();
            $metrics->addApplication($process['name']);

            if (null === $process['exit'] || null === $process['duration']) {
                $metrics->incrementNotExitedProcesses();

                continue;
            }

            $durationList[] = $process['duration'];

            $metrics->updateDuration($process['duration']);
        }

        $metrics->updateTotal($durationList);

        return $metrics;
    }
}
