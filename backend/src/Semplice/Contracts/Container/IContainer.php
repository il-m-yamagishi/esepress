<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Contracts\Container;

use Closure;
use Psr\Container\ContainerInterface;

/**
 * Dependency Injection Container Interface
 */
interface IContainer extends ContainerInterface
{
    /**
     * Bind instance
     * If it is already bound, it emits exception.
     *
     * @template T
     * @param string $abstract
     * @psalm-param class-string<T> $abstract
     * @param T $instance
     * @return void
     * @throws AlreadyBoundException
     */
    public function instance(string $abstract, object $instance): void;

    /**
     * Bind instanciation way callback
     * If it is already bound, it emits exception.
     *
     * @param string $abstract
     * @psalm-param class-string $abstract
     * @param Closure $factory
     * @psalm-param Closure(IContainer):object $factory
     * @return void
     * @throws AlreadyBoundException
     */
    public function factory(string $abstract, Closure $factory): void;

    /**
     * Bind interface/abstract to concrete class
     * If it is already bound, it emits exception.
     *
     * @param string $abstract
     * @psalm-param class-string $abstract
     * @param string $concrete
     * @psalm-param class-string $concrete
     * @return void
     * @throws AlreadyBoundException
     */
    public function bind(string $abstract, string $concrete): void;

    /**
     * Bind instance
     *
     * @template T
     * @param string $abstract
     * @psalm-param class-string<T> $abstract
     * @param T $instance
     * @return void
     */
    public function forceInstance(string $abstract, object $instance): void;

    /**
     * Bind instanciation way callback
     *
     * @template T
     * @param string $abstract
     * @psalm-param class-string<T> $abstract
     * @param Closure $factory
     * @psalm-param Closure(IContainer):T $factory
     * @return void
     */
    public function forceFactory(string $abstract, Closure $factory): void;

    /**
     * Bind interface/abstract to concrete class
     *
     * @param string $abstract
     * @psalm-param class-string $abstract
     * @param string $concrete
     * @psalm-param class-string $concrete
     * @return void
     */
    public function forceBind(string $abstract, string $concrete): void;

    /**
     * Gets instance from container
     *
     * @template T
     * @param string $id
     * @psalm-param class-string<T> $id
     * @return object
     * @psalm-return T
     * @throws CouldNotBeResolvedException
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function get(string $id): object;

    /**
     * Invokes callback with auto injection
     *
     * @template T
     * @param Closure $callback
     * @psalm-param Closure(...mixed):T $callback
     * @param array<string, mixed> $parameters additional concrete parameters
     * @return mixed callback result
     * @psalm-return T
     * @throws CouldNotBeResolvedException
     */
    public function call(Closure $callback, array $parameters = []): mixed;
}
