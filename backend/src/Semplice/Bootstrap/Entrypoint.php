<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Bootstrap;

use Psr\Log\LoggerInterface;
use Semplice\Container\Container;
use Semplice\Contracts\Container\IContainer;

class Entrypoint
{
    public readonly ErrorHandler $error_handler;
    public readonly IContainer $container;

    /**
     * Constructor, no auto-injection because it has a container itself.
     *
     * @param LoggerInterface $logger
     * @param \Semplice\Contracts\Container\IServiceLocator[] $service_locators
     */
    public function __construct(
        public readonly LoggerInterface $logger,
        array $service_locators,
    ) {
        $this->error_handler = new ErrorHandler($this->logger);
        $this->container = new Container($service_locators);
    }
}
