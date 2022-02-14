<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace EsePress\Contracts\Container;

/**
 * Provides functions with dependency injection container
 */
interface IServiceProvider
{
    /**
     * Interacts with IContainer
     * Get or set will be supported
     *
     * @param IContainer $container
     * @return void
     */
    public function provide(IContainer $container): void;
}
