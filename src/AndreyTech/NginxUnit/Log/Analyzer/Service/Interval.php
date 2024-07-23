<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service;

use LogicException;

final class Interval
{
    private const SECONDS_IN_HOUR = 3600;
    private const SECONDS_IN_DAY = 86400;

    public function __construct(
        private readonly int $fromTimestamp,
        private readonly int $toTimestamp
    ) {
        if ($this->fromTimestamp < 0) {
            throw new LogicException(
                sprintf('From timestamp "%u" must be greater than 0.', $this->fromTimestamp)
            );
        }

        if ($this->toTimestamp < 0) {
            throw new LogicException(
                sprintf('To timestamp "%u" must be greater than 0.', $this->toTimestamp)
            );
        }

        if ($fromTimestamp > $toTimestamp) {
            throw new LogicException(
                sprintf('From timestamp "%u" is more then to timestamp "%u".', $fromTimestamp, $toTimestamp)
            );
        }
    }

    public function getFromTimestamp(): int
    {
        return $this->fromTimestamp;
    }

    public function getToTimestamp(): int
    {
        return $this->toTimestamp;
    }

    public function getHours(): int
    {
        return (int) ceil(($this->toTimestamp - $this->fromTimestamp) / self::SECONDS_IN_HOUR);
    }

    public function getDays(): int
    {
        return (int) ceil(($this->toTimestamp - $this->fromTimestamp) / self::SECONDS_IN_DAY);
    }
}
