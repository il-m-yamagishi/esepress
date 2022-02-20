<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Http;

use Semplice\Contracts\Container\IContainer;
use Semplice\Contracts\Container\IServiceLocator;
use Semplice\Contracts\Http\IHttpErrorHandler;
use Semplice\Contracts\Http\IHttpResponseEmitter;
use Semplice\Contracts\Http\IHttpRunner;

class HttpServiceLocator implements IServiceLocator
{
    /**
     * {@inheritDoc}
     */
    public function getStaticBindings(): array
    {
        return [
            IHttpResponseEmitter::class => HttpResponseEmitter::class,
            IHttpRunner::class => HttpRunner::class,
            IHttpErrorHandler::class => ProductionHttpErrorHandler::class,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(IContainer $container): void
    {
        // noop
    }
}
