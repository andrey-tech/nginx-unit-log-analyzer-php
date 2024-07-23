<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Gnuplot\CommandFile;

use AndreyTech\NginxUnit\Log\Analyzer\Exception as AnalyzerException;

final class Creator
{
    /**
     * @var resource
     */
    private $fileHandler;

    public function __construct()
    {
        $this->fileHandler = $this->createTempFile();
    }

    public function create(string $contents): void
    {
        if (false === fwrite($this->fileHandler, $contents)) {
            throw new AnalyzerException(
                sprintf('Can not write to temporary command file "%s".', $this->getAbsoluteFileName())
            );
        }
    }

    public function getAbsoluteFileName(): string
    {
        $metaData = stream_get_meta_data($this->fileHandler);

        $fileName = $metaData['uri'] ?? null;

        if (null === $fileName) {
            throw new AnalyzerException('Can not get name of temporary command file.');
        }

        return $fileName;
    }

    /**
     * @return resource
     */
    private function createTempFile()
    {
        $fileHandler = tmpfile();

        if (false === $fileHandler) {
            throw new AnalyzerException('Can not create temporary command file.');
        }

        return $fileHandler;
    }
}
