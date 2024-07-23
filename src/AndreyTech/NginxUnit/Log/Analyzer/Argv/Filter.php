<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Argv;

use DateTimeImmutable;
use Stringable;

final class Filter implements Stringable
{
    private ?int $fromTimestamp;
    private ?int $toTimestamp;

    public function __construct(
        private readonly ?DatetimeImmutable $fromDatetime = null,
        private readonly ?DateTimeImmutable $toDatetime = null,
        private readonly ?string $applicationName = null
    ) {
        $this->fromTimestamp = $fromDatetime?->getTimestamp();
        $this->toTimestamp = $toDatetime?->getTimestamp();
    }

    public function getApplicationName(): ?string
    {
        return $this->applicationName;
    }

    public function getFromTimestamp(): ?int
    {
        return $this->fromTimestamp;
    }

    public function getToTimestamp(): ?int
    {
        return $this->toTimestamp;
    }

    public function __toString(): string
    {
        $filterParts = [];

        if ($this->fromDatetime) {
            $filterParts[] = sprintf('from: %s', $this->fromDatetime->format('Y-m-d H:i:s e'));
        }

        if ($this->toDatetime) {
            $filterParts[] = sprintf('to: %s', $this->toDatetime->format('Y-m-d H:i:s e'));
        }

        if (null !== $this->applicationName) {
            $filterParts[] = sprintf('application: %s', $this->applicationName);
        }

        return sprintf('Filters: %s', implode(', ', $filterParts) ?: 'none');
    }
}
