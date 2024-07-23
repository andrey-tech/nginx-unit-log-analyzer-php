<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Renderer;

final class Helper
{
    /**
     * @psalm-pure
     */
    public static function secondsToTime(int $seconds): string
    {
        $secondsInMinute = 60;
        $secondsInHour = 3600;
        $secondsInDay = 86400;

        if ($seconds < $secondsInMinute) {
            return sprintf('%ds', $seconds);
        }

        $hourSeconds = $seconds % $secondsInDay;
        $minuteSeconds = $hourSeconds % $secondsInHour;
        $remainingSeconds = $minuteSeconds % $secondsInMinute;

        /** @var array<string, int> */
        $sections = [
            'd' => (int) floor($seconds / $secondsInDay),
            'h' => (int) floor($hourSeconds / $secondsInHour),
            'm' => (int) floor($minuteSeconds / $secondsInMinute),
            's' => (int) ceil($remainingSeconds),
        ];

        $timeParts = [];

        foreach ($sections as $name => $value) {
            if ($value > 0) {
                $timeParts[] = sprintf('%u%s', $value, $name);
            }
        }

        return implode(' ', $timeParts);
    }
}
