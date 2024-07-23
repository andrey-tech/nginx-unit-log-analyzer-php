<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph;

use AndreyTech\NginxUnit\Log\Analyzer\Exception as AnalyzerException;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Interval;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Calculator\Metric;
use DateTimeImmutable;
use DateTimeZone;
use Exception;

abstract class Exporter
{
    /**
     * @var resource
     */
    private $fileHandler;

    public function __construct()
    {
        $this->fileHandler = $this->createTempFile();
    }

    /**
     * @param list<Metric> $metricList
     */
    abstract public function export(array $metricList, Interval $dayInterval, DateTimeZone $reportTimezone): void;

    abstract public function getName(): string;

    abstract public function getParameterName(): string;

    abstract public function getParameterFormat(): string;

    public function getAbsoluteFileName(): string
    {
        $metaData = stream_get_meta_data($this->fileHandler);

        $fileName = $metaData['uri'] ?? null;

        if (null === $fileName) {
            throw new AnalyzerException('Can not get name of temporary CSV file.');
        }

        return $fileName;
    }

    /**
     * @throws Exception
     */
    protected function formatDateTime(int $timestamp, DateTimeZone $reportTimezone): string
    {
        return (new DateTimeImmutable('now', $reportTimezone))->setTimestamp($timestamp)->format('Y-m-d H:i:s');
    }

    /**
     * @param array<string|int|null> $row
     */
    protected function save(array $row): void
    {
        if (false === fputcsv($this->fileHandler, $row)) {
            throw new AnalyzerException(
                sprintf('Can not write row to temporary CSV file "%s".', $this->getAbsoluteFileName())
            );
        }
    }

    /**
     * @return resource
     */
    private function createTempFile()
    {
        $fileHandler = tmpfile();

        if (false === $fileHandler) {
            throw new AnalyzerException('Can not create temporary CSV file.');
        }

        return $fileHandler;
    }
}
