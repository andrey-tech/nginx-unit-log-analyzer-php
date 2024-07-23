<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Parser;

use Stringable;

final class Stats implements Stringable
{
    public int $lines = 0;

    public int $start = 0;
    public int $exit = 0;

    public int $startAdded = 0;
    public int $exitAdded = 0;
    public int $exitSkipped = 0;

    public int $skippedByApplicationNameFilter = 0;
    public int $skippedByFromTimestampFilter = 0;
    public int $skippedByToTimestampFilter = 0;

    public function __toString(): string
    {
        return sprintf(
            <<<'EOL'
                Parser stats:
                 - total lines: %u
                 - started lines: %u (added: %u)
                 - exited lines: %u (added: %u, skipped: %u)
                 - skipped by filter: %u (app name: %u, from: %u, to: %u)
                EOL,
            $this->lines,
            $this->start,
            $this->startAdded,
            $this->exit,
            $this->exitAdded,
            $this->exitSkipped,
            $this->skippedByApplicationNameFilter +
                $this->skippedByFromTimestampFilter +
                $this->skippedByToTimestampFilter,
            $this->skippedByApplicationNameFilter,
            $this->skippedByFromTimestampFilter,
            $this->skippedByToTimestampFilter,
        );
    }
}
