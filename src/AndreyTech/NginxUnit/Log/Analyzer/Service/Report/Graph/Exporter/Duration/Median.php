<?php

/**
 * @author    andrey-tech
 * @copyright 2024 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

namespace AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Exporter\Duration;

use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Calculator\Metric;
use AndreyTech\NginxUnit\Log\Analyzer\Service\Report\Graph\Exporter\Duration;

final class Median extends Duration
{
    public function getName(): string
    {
        return 'Median duration of NGINX Unit processes within hour';
    }

    protected function getDuration(Metric $metric): ?int
    {
        return $metric->getMedianDuration();
    }
}
