<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Contracts\Bootstrap;

use Psr\Log\LoggerInterface;

/**
 * PSR-4 Logger factory
 */
interface ILoggerFactory
{
    /**
     * Creates logger instance
     *
     * @param string $name Application name
     * @return LoggerInterface
     */
    public function createLogger(string $name): LoggerInterface;
}
