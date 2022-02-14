<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace EsePress\Http;

use EsePress\Contracts\Container\IContainer;
use EsePress\Contracts\Container\IServiceProvider;

class HttpServiceProvider implements IServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function provide(IContainer $container): void
    {
        $this->provideRequestHandler($container);
        $this->provideErrorHandler($container);
    }

    protected function provideRequestHandler(IContainer $container): void
    {

    }

    protected function provideErrorHandler(IContainer $container): void
    {
    }
}
