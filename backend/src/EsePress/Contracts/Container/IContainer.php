<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace EsePress\Contracts\Container;

use Psr\Container\ContainerInterface;

/**
 * Processes any throwable error
 */
interface IContainer extends ContainerInterface
{
    /**
     * Bind interface/abstract to concrete classes
     *
     * @param class-string $abstract
     * @param class-string $concrete
     * @return void
     */
    public function bind(string $abstract, string $concrete): void;
}
