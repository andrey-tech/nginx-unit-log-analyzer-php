<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph;

use AndreyTech\NginxUnit\Log\Analyzer\Service\Parser\Processes;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Calculator\Metric;
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

            if (null === $process['exit'] || null === $process['duration']) {
                $metrics->incrementNotExitedProcesses();

                continue;
            }

            $duration = $process['duration'];

            $metrics->updateDuration($duration);
            $durationList[] = $duration;
        }

        $metrics->updateTotal($durationList);

        return $metrics;
    }
}
