<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Container;

use Closure;
use Semplice\Contracts\Container\AlreadyBoundException;
use Semplice\Contracts\Container\IContainer;

/**
 * Dependency Injection Container
 */
class Container implements IContainer
{
    /**
     * Manually registered instances
     *
     * @var array<string, object>
     * @psalm-var array<class-string, object>
     */
    private array $instances = [];

    /**
     * Manually registered factories
     *
     * @var array<string, Closure>
     * @psalm-var array<class-string, Closure(IContainer):object>
     */
    private array $factories = [];

    /**
     * Manually registered bind classes
     *
     * @var array<string, string>
     * @psalm-var array<class-string, class-string>
     */
    private array $bindings = [];

    private readonly ClassResolver $resolver;

    public function __construct(?ClassResolver $resolver = null)
    {
        $this->resolver = $resolver ?: new ClassResolver($this);
    }

    /**
     * {@inheritDoc}
     */
    public function instance(string $abstract, object $instance): void
    {
        if (array_key_exists($abstract, $this->instances)) {
            throw new AlreadyBoundException(sprintf('abstract %s is already bound', $abstract));
        }
        assert($instance instanceof $abstract, 'The instance must extend/implement abstract');
        $this->instances[$abstract] = $instance;
    }

    /**
     * {@inheritDoc}
     */
    public function factory(string $abstract, Closure $factory): void
    {
        if (array_key_exists($abstract, $this->factories)) {
            throw new AlreadyBoundException(sprintf('abstract %s is already bound', $abstract));
        }
        $this->factories[$abstract] = $factory;
    }

    /**
     * {@inheritDoc}
     */
    public function bind(string $abstract, string $concrete): void
    {
        if (array_key_exists($abstract, $this->bindings)) {
            throw new AlreadyBoundException(sprintf('abstract %s is already bound', $abstract));
        }
        $this->bindings[$abstract] = $concrete;
    }

    /**
     * {@inheritDoc}
     */
    public function forceInstance(string $abstract, object $instance): void
    {
        assert($instance instanceof $abstract, 'The instance must extend/implement abstract');
        $this->instances[$abstract] = $instance;
    }

    /**
     * {@inheritDoc}
     */
    public function forceFactory(string $abstract, Closure $factory): void
    {
        $this->factories[$abstract] = $factory;
    }

    /**
     * {@inheritDoc}
     */
    public function forceBind(string $abstract, string $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $id): mixed
    {
        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        } elseif (array_key_exists($id, $this->factories)) {
            $instance = $this->factories[$id]($this);
            assert($instance instanceof $id, 'The instance must extends/implement abstract');
            return $instance;
        } elseif (array_key_exists($id, $this->bindings)) {
            return $this->get($this->bindings[$id]);
        }

        return $this->resolver->resolve($id);
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $id): bool
    {
        if (array_key_exists($id, $this->instances)) {
            return true;
        } elseif (array_key_exists($id, $this->factories)) {
            return true;
        } elseif (array_key_exists($id, $this->bindings)) {
            return true;
        }

        return $this->resolver->canResolve($id);
    }

    /**
     * {@inheritDoc}
     */
    public function call(Closure $callback, array $parameters = []): mixed
    {
        throw new \LogicException('Not implemented yet');
    }
}
