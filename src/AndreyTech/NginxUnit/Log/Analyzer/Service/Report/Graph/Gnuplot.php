<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph;

use AndreyTech\NginxUnit\Log\Analyzer\Exception as AnalyzerException;
use LogicException;

final class Gnuplot
{
    private const GNU_PLOT = 'gnuplot';

    public function check(): void
    {
        $this->run('', '--version');
    }

    public function run(string $arguments = '', string $options = ''): void
    {
        if ('' === $arguments && '' === $options) {
            throw new LogicException('Arguments or options is missing for gnuplot util.');
        }

        $command = sprintf('%s %s %s', self::GNU_PLOT, $arguments, $options);

        $statusCode = null;
        /** @psalm-suppress TypeDoesNotContainType */
        if (false === passthru($command, $statusCode)) {
            throw new AnalyzerException(
                sprintf('Error execution command "%s".', $command)
            );
        }

        if (0 !== $statusCode) {
            throw new AnalyzerException(
                sprintf('Status of command "%s" is "%u".', $command, $statusCode)
            );
        }
    }
}
