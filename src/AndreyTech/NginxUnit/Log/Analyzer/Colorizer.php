<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer;

final class Colorizer
{
    private const TEMPLATE_YELLOW = '<fg=yellow>%s</>';
    private const TEMPLATE_YELLOW_BOLD = '<fg=yellow;options=bold>%s</>';

    private const TEMPLATE_RED = '<fg=red>%s</>';
    private const TEMPLATE_RED_BOLD = '<fg=red;options=bold>%s</>';

    private const TEMPLATE_WHITE_BOLD = '<fg=white;options=bold>%s</>';

    public static function yellow(mixed $value): string
    {
        return self::colorize(self::TEMPLATE_YELLOW, $value);
    }

    public static function yellowBold(mixed $value): string
    {
        return self::colorize(self::TEMPLATE_YELLOW_BOLD, $value);
    }

    public static function red(mixed $value): string
    {
        return self::colorize(self::TEMPLATE_RED, $value);
    }

    public static function redBold(mixed $value): string
    {
        return self::colorize(self::TEMPLATE_RED_BOLD, $value);
    }

    public static function whiteBold(mixed $value): string
    {
        return self::colorize(self::TEMPLATE_WHITE_BOLD, $value);
    }

    private static function colorize(string $template, mixed $value): string
    {
        return sprintf($template, (string) $value);
    }
}
