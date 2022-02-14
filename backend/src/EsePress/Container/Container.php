<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace EsePress\Container;

use EsePress\Contracts\Container\IContainer;

/**
 * Processes any throwable error
 */
class Container implements IContainer
{
    /**
     * Manually registered bind classes
     *
     * @var array<string, string>
     * @psalm-var array<class-string, class-string>
     */
    private array $binded = [];

    public function bind(string $abstract, string $concrete): void
    {
        $this->binded[$abstract] = $concrete;
    }

    /**
     * Get from DI container
     *
     * @param class-string $id
     * @return mixed
     */
    public function get(string $id): mixed
    {
        if ($this->binded[$id]) {
            return $this->get($this->binded[$id]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $id): bool
    {
        if ($this->binded[$id]) {
            return true;
        }
    }
}
