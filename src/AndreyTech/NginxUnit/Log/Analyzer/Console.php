<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Helper\ProgressBar;

final class Console
{
    private ConsoleOutput $consoleOutput;

    public function __construct()
    {
        $this->consoleOutput = new ConsoleOutput();
    }

    public function getConsoleOutput(): ConsoleOutput
    {
        return $this->consoleOutput;
    }

    public function emptyLine(): void
    {
         $this->consoleOutput->writeln('');
    }

    public function message(string $message): void
    {
         $this->consoleOutput->writeln($message);
    }

    public function warning(string $message): void
    {
         $this->consoleOutput->writeln(
             Colorizer::yellowBold($message)
         );
    }

    public function error(string $message): void
    {
         $this->consoleOutput->writeln(
             Colorizer::redBold($message)
         );
    }

    public function makeProgressBar(int $max = 0): ProgressBar
    {
        return new ProgressBar($this->consoleOutput, $max);
    }
}
