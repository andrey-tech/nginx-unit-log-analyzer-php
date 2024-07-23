<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Top;

use AndreyTech\NginxUnit\Log\Analyzer\Service\Parser\Processes;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Top\Calculator\Metrics;
use Generator;

/**
 * @psalm-import-type P from Processes
 */
final class Calculator
{
    /**
     * @psalm-param Generator<P> $processIterator
     */
    public function calculate(Generator $processIterator): Metrics
    {
        $metrics = new Metrics();

        foreach ($processIterator as $process) {
            $metrics->addProcess(
                $process['id'],
                $process['name'],
                $process['start'],
                $process['startLine'],
                $process['exit'],
                $process['exitLine'],
                $process['duration']
            );
        }

        return $metrics;
    }
}
