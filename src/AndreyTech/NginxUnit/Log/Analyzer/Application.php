<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer;

use AndreyTech\NginxUnit\Log\Analyzer\Service\Parser;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Parser\Processes;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Factory;
use Symfony\Component\Console\Command\Command;
use Throwable;

final class Application
{
    private Argv $argv;
    private Console $console;
    private Stats $stats;

    public function __construct()
    {
        $this->argv = new Argv();
        $this->console = new Console();
        $this->stats = new Stats();
    }

    public function run(): int
    {
        $this->stats->start();

        $this->console->message('Starting nginx-unit-log-analyzer');

        try {
            $this->argv->buildArgvInput();

            $processes = $this->getProcesses();

            if ($processes->isEmpty()) {
                $this->console->warning(
                    sprintf('There are no processes found in the log file "%s"', $this->argv->handleArgumentLogFile())
                );

                $exitCode = Command::INVALID;
                $this->printStats($exitCode);

                return $exitCode;
            }

            $this->createReport($processes);
            $exitCode = Command::SUCCESS;

        } catch (Throwable $exception) {
            $this->console->error(
                sprintf('ERROR: %s (%s:%u)', $exception->getMessage(), $exception->getFile(), $exception->getLine())
            );

            $exitCode = Command::FAILURE;
        }

        $this->printStats($exitCode);

        return $exitCode;
    }

    private function getProcesses(): Processes
    {
        $parser = new Parser($this->console);

        $logFile = $this->argv->handleArgumentLogFile();
        $logTimezone = $this->argv->handleOptionLogTimezone();

        $logFilter = $this->argv->handleOptionLogFilter();
        $this->console->message((string) $logFilter);

        return $parser->parse($logFile, $logFilter, $logTimezone);
    }

    private function createReport(Processes $processes): void
    {
        $factory = new Factory($this->console, $this->argv);

        $reportType = $this->argv->handleOptionReportType();
        $this->console->message(
            sprintf('Report type: %s', $reportType)
        );

        $report = $factory->make($reportType);
        $report->create($processes);
    }

    private function printStats(int $exitCode): void
    {
        $this->stats->finish();
        $this->stats->setExitCode($exitCode);

        $this->console->emptyLine();
        $this->console->message('Finished nginx-unit-log-analyzer');
        $this->console->message((string) $this->stats);
    }
}
