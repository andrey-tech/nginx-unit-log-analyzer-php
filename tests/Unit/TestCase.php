<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace Test\Unit;

use JsonException;
use PHPUnit\Framework\TestCase as UnitTestCase;
use RuntimeException;

class TestCase extends UnitTestCase
{
    private const DATA_DIR = 'Data';

    /**
     * @throws JsonException
     */
    protected function loadJsonFile(string $file): array
    {
        return (array) json_decode($this->loadFile($file), true, 512, JSON_THROW_ON_ERROR);
    }

    protected function loadFile(string $file): string
    {
        $contents = file_get_contents($file);

        if (false === $contents) {
            throw new RuntimeException(
                sprintf('Can not load file "%s".', $file)
            );
        }

        return $contents;
    }

    protected function getAbsoluteFileName(string $testDir, string $fileName, ?int $testCaseNumber = null): string
    {
        $file = sprintf(
            '%s/%s/%s/%s',
            __DIR__,
            self::DATA_DIR,
            $testDir,
            null === $testCaseNumber ? $fileName : sprintf('%u/%s', $testCaseNumber, $fileName)
        );

        if (!is_file($file)) {
            throw new RuntimeException(
                sprintf('Can not find file "%s".', $file)
            );
        }

        if (!is_readable($file)) {
            throw new RuntimeException(
                sprintf('Can not read file "%s".', $file)
            );
        }

        return $file;
    }
}
