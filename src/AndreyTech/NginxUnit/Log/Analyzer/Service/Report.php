<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service;

use AndreyTech\NginxUnit\Log\Analyzer\Argv;
use AndreyTech\NginxUnit\Log\Analyzer\Console;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Parser\Processes;

abstract class Report
{
    public function __construct(
        protected readonly Console $console,
        protected readonly Argv $argv
    ) {
    }

    abstract public function create(Processes $processes): void;
}
