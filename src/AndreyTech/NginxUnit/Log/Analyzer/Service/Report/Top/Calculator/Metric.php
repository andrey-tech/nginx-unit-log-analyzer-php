<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Top\Calculator;

final class Metric
{
    public function __construct(
        private readonly int $processId,
        private readonly string $applicationName,
        private readonly int $startTimestamp,
        private readonly int $startLine,
        private readonly int $exitTimestamp,
        private readonly int $exitLine,
        private readonly int $duration
    ) {
    }

    public function getProcessId(): int
    {
        return $this->processId;
    }

    public function getStartTimestamp(): int
    {
        return $this->startTimestamp;
    }

    public function getExitTimestamp(): int
    {
        return $this->exitTimestamp;
    }

    public function getStartLine(): int
    {
        return $this->startLine;
    }

    public function getExitLine(): int
    {
        return $this->exitLine;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getApplicationName(): string
    {
        return $this->applicationName;
    }
}
