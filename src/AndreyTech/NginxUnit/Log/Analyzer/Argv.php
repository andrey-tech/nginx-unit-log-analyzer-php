<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer;

use AndreyTech\NginxUnit\Log\Analyzer\Argv\Filter;
use AndreyTech\NginxUnit\Log\Analyzer\Exception as AnalyzerException;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

final class Argv
{
    private const DEFAULT_REPORT_TYPE = 'graph';
    private const DEFAULT_REPORT_FILE_EXTENSION = 'png';
    private const DEFAULT_GRAPH_TYPES = [ 'quantity', 'maximum', 'median' ];

    private ArgvInput $argvInput;

    public function __construct()
    {
        $this->argvInput = new ArgvInput();
    }

    public function buildArgvInput(): void
    {
        $definition = new InputDefinition();

        $this->buildArgvInputLog($definition);
        $this->buildArgvInputReport($definition);
        $this->buildArgvInputFilters($definition);
        $this->buildArgvInputGraph($definition);

        $this->argvInput = new ArgvInput(null, $definition);
    }

    private function buildArgvInputLog(InputDefinition $definition): void
    {
        $definition->addArgument(
            new InputArgument(
                'log-file',
                InputArgument::OPTIONAL,
                'Path to NGINX Unit log file'
            )
        );

        $definition->addOption(
            new InputOption(
                'log-timezone',
                null,
                InputOption::VALUE_REQUIRED,
                'Timezone in log file',
                date_default_timezone_get()
            )
        );
    }

    private function buildArgvInputReport(InputDefinition $definition): void
    {
        $definition->addOption(
            new InputOption(
                'report-type',
                null,
                InputOption::VALUE_REQUIRED,
                'Type of report',
                self::DEFAULT_REPORT_TYPE
            )
        );

        $definition->addOption(
            new InputOption(
                'report-timezone',
                null,
                InputOption::VALUE_REQUIRED,
                'Timezone for report',
                date_default_timezone_get()
            )
        );
    }

    private function buildArgvInputFilters(InputDefinition $definition): void
    {
        $definition->addOption(
            new InputOption(
                'filter-start-time-from',
                null,
                InputOption::VALUE_REQUIRED,
                'Start processes time from',
            )
        );

        $definition->addOption(
            new InputOption(
                'filter-start-time-to',
                null,
                InputOption::VALUE_REQUIRED,
                'Start processes time to',
            )
        );

        $definition->addOption(
            new InputOption(
                'filter-application-name',
                null,
                InputOption::VALUE_REQUIRED,
                'Name of application',
            )
        );
    }

    private function buildArgvInputGraph(InputDefinition $definition): void
    {
        $definition->addOption(
            new InputOption(
                'graph-file-name',
                null,
                InputOption::VALUE_REQUIRED,
                'Output file of graph'
            )
        );

        $definition->addOption(
            new InputOption(
                'graph-types',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'List of graph types',
                self::DEFAULT_GRAPH_TYPES
            )
        );
    }

    public function handleArgumentLogFile(): string
    {
        $logFile = (string) $this->argvInput->getArgument('log-file');

        if (empty($logFile)) {
            throw new AnalyzerException(
                'Missing required argument "path to NGINX Unit log file".'
            );
        }

        if (!is_file($logFile)) {
            throw new AnalyzerException(
                sprintf('Can not find NGINX Unit log file "%s".', $logFile)
            );
        }

        if (!is_readable($logFile)) {
            throw new AnalyzerException(
                sprintf('Can not read NGINX Unit log file "%s".', $logFile)
            );
        }

        return $logFile;
    }

    public function handleOptionReportType(): string
    {
        return (string) $this->argvInput->getOption('report-type');
    }

    public function handleOptionFilterStartTimeFrom(): ?DateTimeImmutable
    {
        $startTime = (string) $this->argvInput->getOption('filter-start-time-from');

        if (empty($startTime)) {
            return null;
        }

        try {
            $datetime = new DateTimeImmutable($startTime);
        } catch (Exception) {
            throw new AnalyzerException(
                sprintf('Can not parse value of "--filter-start-time-from" option "%s".', $startTime)
            );
        }

        return $datetime;
    }

    public function handleOptionFilterStartTimeTo(): ?DateTimeImmutable
    {
        $startTime = (string) $this->argvInput->getOption('filter-start-time-to');

        if (empty($startTime)) {
            return null;
        }

        try {
            $datetime = new DateTimeImmutable($startTime);
        } catch (Exception) {
            throw new AnalyzerException(
                sprintf('Can not parse value of "--filter-start-time-from" option "%s".', $startTime)
            );
        }

        return $datetime;
    }

    public function handleOptionFilterApplicationName(): ?string
    {
        $applicationName = (string) $this->argvInput->getOption('filter-application-name');

        if (empty($applicationName)) {
            return null;
        }

        return $applicationName;
    }

    public function handleOptionLogTimezone(): DateTimeZone
    {
        $timezone = (string) $this->argvInput->getOption('log-timezone');

        if (empty($timezone)) {
            throw new AnalyzerException('Value of "--log-timezone" option is empty.');
        }

        try {
            $dateTimeZone = new DateTimeZone($timezone);
        } catch (Exception) {
            throw new AnalyzerException(
                sprintf('Can not parse value of "--log-timezone" option "%s".', $timezone)
            );
        }

        return $dateTimeZone;
    }

    public function handleOptionReportTimezone(): DateTimeZone
    {
        $timezone = (string) $this->argvInput->getOption('report-timezone');

        if (empty($timezone)) {
            throw new AnalyzerException('Value of "--report-timezone" option is empty.');
        }

        try {
            $dateTimeZone = new DateTimeZone($timezone);
        } catch (Exception) {
            throw new AnalyzerException(
                sprintf('Can not parse value of "--report-timezone" option "%s".', $timezone)
            );
        }

        return $dateTimeZone;
    }

    public function handleOptionGraphFileName(): string
    {
        $graphFileName = (string) $this->argvInput->getOption('graph-file-name');

        if (empty($graphFileName)) {
            return sprintf('%s.%s', $this->handleArgumentLogFile(), self::DEFAULT_REPORT_FILE_EXTENSION);
        }

        return $graphFileName;
    }

    /**
     * @return string[]
     */
    public function handleOptionGraphTypes(): array
    {
        /** @var string[] $graphTypes */
        $graphTypes = (array) $this->argvInput->getOption('graph-types');

        if (empty($graphTypes)) {
            throw new AnalyzerException('Empty value of "--graph-types" option.');
        }

        return $graphTypes;
    }

    public function handleOptionLogFilter(): Filter
    {
        return new Filter(
            $this->handleOptionFilterStartTimeFrom(),
            $this->handleOptionFilterStartTimeTo(),
            $this->handleOptionFilterApplicationName()
        );
    }
}
