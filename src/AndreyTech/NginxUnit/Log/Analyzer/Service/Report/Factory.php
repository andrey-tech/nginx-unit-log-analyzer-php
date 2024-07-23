<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Report;

use AndreyTech\NginxUnit\Log\Analyzer\Argv;
use AndreyTech\NginxUnit\Log\Analyzer\Console;
use AndreyTech\NginxUnit\Log\Analyzer\Exception as AnalyzerException;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report;

final class Factory
{
    public function __construct(
        private readonly Console $console,
        private readonly Argv $argv
    ) {
    }

    public function make(string $reportType): Report
    {
        switch ($reportType) {
            case 'day':
                return new Day($this->console, $this->argv);
            case 'top':
                return new Top($this->console, $this->argv);
            case 'graph':
                return new Graph($this->console, $this->argv);
        }

        throw new AnalyzerException(
            sprintf('Unknown report type "%s".', $reportType)
        );
    }
}
