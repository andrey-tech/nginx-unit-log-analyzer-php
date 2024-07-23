<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph;

use AndreyTech\NginxUnit\Log\Analyzer\Argv;
use AndreyTech\NginxUnit\Log\Analyzer\Console;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Gnuplot\CommandFile\Builder;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Gnuplot\CommandFile\Creator;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Gnuplot\Plot;

final class Plotter
{
    private const PLOT_WIDTH = 1920;
    private const PLOT_HEIGHT = 540;

    private readonly Gnuplot $gnuplot;
    private readonly Builder $builder;
    private readonly Creator $creator;

    public function __construct(
        private readonly Console $console,
        private readonly Argv $argv
    ) {
        $this->gnuplot = new Gnuplot();
        $this->builder = new Builder();
        $this->creator = new Creator();
    }

    public function checkGnuplot(): void
    {
        $this->console->message('Checking gpuplot util');
        $this->gnuplot->check();
    }

    /**
     * @param list<string> $applicationList
     * @param list<Exporter> $exporterList
     */
    public function createCommandFile(array $applicationList, array $exporterList): void
    {
        $this->build($applicationList, $exporterList);

        $this->creator->create($this->builder->build());
    }

    public function plot(): void
    {
        $this->console->message(
            sprintf('Creating graph file "%s"', $this->argv->handleOptionGraphFileName())
        );

        $this->gnuplot->run($this->creator->getAbsoluteFileName());
    }

    /**
     * @param list<string> $applicationList
     * @param list<Exporter> $exporterList
     */
    private function build(array $applicationList, array $exporterList): void
    {
        $totalPlots = count($exporterList);

        $this->builder->setPngTerminal($this->calculateGraphSize($totalPlots));
        $this->builder->setOutput($this->argv->handleOptionGraphFileName());

        $this->builder->setDatafileSeparator();
        $this->builder->setTimefmt();

        $this->builder->setGridXtics();
        $this->builder->setGridYtics();

        $this->builder->setXdata();
        $this->builder->setXtics();

        $this->builder->setMultiplot($totalPlots);

        $this->buildPlots($applicationList, $exporterList);

        $this->builder->unsetMultiplot();
    }

    /**
     * @param list<string> $applicationList
     * @param list<Exporter> $exporterList
     */
    private function buildPlots(array $applicationList, array $exporterList): void
    {
        $lastExporterIndex = count($exporterList) - 1;

        foreach ($exporterList as $exporterIndex => $exporter) {

            $this->builder->setTitle($exporter->getName());
            $this->builder->setYlabel($exporter->getParameterName());

            $parameterFormat = $exporter->getParameterFormat();
            if ($parameterFormat) {
                $this->builder->setYtics($parameterFormat);
            }

            $this->builder->setYrange();

            if ($exporterIndex === $lastExporterIndex) {
                $this->builder->setXlabel(
                    sprintf('Time (TZ: %s)', $this->argv->handleOptionReportTimezone()->getName())
                );
            }

            $plot = new Plot();

            foreach ($applicationList as $applicationIndex => $applicationName) {
                $plot->addLine($exporter->getAbsoluteFileName(), [ 1, $applicationIndex + 2 ], $applicationName);
            }

            $this->builder->plot($plot);
        }
    }

    /**
     * @return array{int, int}
     */
    private function calculateGraphSize(int $totalPlots): array
    {
        return [ self::PLOT_WIDTH, $totalPlots * self::PLOT_HEIGHT ];
    }
}
