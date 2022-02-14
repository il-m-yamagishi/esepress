<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Contracts\Container;

/**
 * Interacts with dependency injection container
 */
interface IServiceLocator
{
    /**
     * Returns static binding list to be registered
     *
     * @return array<string, string>
     * @psalm-return array<class-string, class-string>
     */
    public function getStaticBindings(): array;

    /**
     * Set instanciation way into container
     *
     * @param IContainer $container
     * @return void
     */
    public function __invoke(IContainer $container): void;
}
