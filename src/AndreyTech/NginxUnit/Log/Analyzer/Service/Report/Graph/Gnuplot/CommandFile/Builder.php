<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Gnuplot\CommandFile;

use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Gnuplot\Plot;

final class Builder
{
    /**
     * @var list<string>
     */
    private array $commands = [];

    public function build(): string
    {
        return implode("\n", $this->commands);
    }

    /**
     * @param array{int, int} $size
     */
    public function setPngTerminal(array $size): void
    {
        $this->addCommand(
            sprintf('set terminal png giant size %u, %u', $size[0], $size[1])
        );
    }

    public function setOutput(string $file): void
    {
        $this->addCommand(
            sprintf("set output '%s'", $file)
        );
    }

    public function setDatafileSeparator(string $separator = ','): void
    {
        $this->addCommand(
            sprintf("set datafile separator '%s'", $separator)
        );
    }

    public function setGridXtics(): void
    {
        $this->addCommand('set grid xtics');
    }

    public function setGridYtics(): void
    {
        $this->addCommand('set grid ytics');
    }

    public function setXdata(string $type = 'time'): void
    {
        $this->addCommand(
            sprintf('set xdata %s', $type)
        );
    }

    public function setTimefmt(string $format = '%Y-%m-%d %H:%M:%S'): void
    {
        $this->addCommand(
            sprintf("set timefmt '%s'", $format)
        );
    }

    public function setXtics(string $format = '%Y-%m-%d\n%H:%M:%S'): void
    {
        $this->addCommand(
            sprintf('set xtics rotate format "%s"', $format)
        );
    }

    public function setYtics(string $format, string $type = 'time'): void
    {
        $this->addCommand(
            sprintf("set ytics %s format '%s'", $type, $format)
        );
    }

    public function setTitle(string $title): void
    {
        $this->addCommand(
            sprintf("set title '%s'", $title)
        );
    }

    public function setXlabel(string $label): void
    {
        $this->addCommand(
            sprintf("set xlabel '%s'", $label)
        );
    }

    public function setYlabel(string $label): void
    {
        $this->addCommand(
            sprintf("set ylabel '%s'", $label)
        );
    }

    public function setYrange(int|string $min = 0, int|string $max = '*'): void
    {
        $this->addCommand(
            sprintf("set yrange [%s:%s]", $min, $max)
        );
    }

    public function setMultiplot(int $rows = 2, int $columns = 1): void
    {
        $this->addCommand(
            sprintf("set multiplot layout %u,%u", $rows, $columns)
        );
    }

    public function unsetMultiplot(): void
    {
        $this->addCommand('unset multiplot');
    }

    public function plot(Plot $plot): void
    {
        $this->addCommand($plot->build());
    }

    private function addCommand(string $command): void
    {
        $this->commands[] = $command;
    }
}
