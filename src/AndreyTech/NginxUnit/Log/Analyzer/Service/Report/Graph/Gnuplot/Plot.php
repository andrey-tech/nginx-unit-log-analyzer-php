<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Gnuplot;

final class Plot
{
    /**
     * @var list<string>
     */
    private array $lines = [];

    /**
     * @param array{int, int} $using
     */
    public function addLine(string $file, array $using, string $title, int $lineWidth = 2): void
    {
        $this->putLine(
            sprintf(
                "'%s' using %u:%u with lines linewidth %u title '%s'",
                $file,
                $using[0],
                $using[1],
                $lineWidth,
                $title
            )
        );
    }

    public function build(): string
    {
        return sprintf('plot %s', implode(", \\\n", $this->lines));
    }

    private function putLine(string $line): void
    {
        $this->lines[] = $line;
    }
}
