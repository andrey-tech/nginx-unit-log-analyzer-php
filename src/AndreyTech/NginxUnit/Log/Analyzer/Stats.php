<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer;

use Stringable;

final class Stats implements Stringable
{
    private float $startTimestamp = 0.0;
    private float $finishTimestamp = 0.0;
    private int $exitCode = 0;

    public function start(): void
    {
        $this->startTimestamp = $this->getCurrentTimestamp();
    }

    public function finish(): void
    {
        $this->finishTimestamp = $this->getCurrentTimestamp();
    }

    public function setExitCode(int $exitCode): void
    {
        $this->exitCode = $exitCode;
    }

    public function __toString(): string
    {
        $deltaTime = (int) round(1000 * ($this->finishTimestamp - $this->startTimestamp));
        $memoryPeakUsage = (int) round(memory_get_peak_usage(true) / 1024 / 1024);
        $memoryUsage = (int) round(memory_get_usage(true) / 1024 / 1024);

        return sprintf(
            'Exit code: %u, Time: %u ms, Memory: %u/%u MiB',
            $this->exitCode,
            $deltaTime,
            $memoryPeakUsage,
            $memoryUsage
        );
    }

    private function getCurrentTimestamp(): float
    {
        return microtime(true);
    }
}
