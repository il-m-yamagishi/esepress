<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Container;

use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionIntersectionType;
use ReflectionParameter;
use ReflectionUnionType;

/**
 * @internal
 * @psalm-internal \Semplice\Container
 */
class ClassResolver
{
    /**
     * Currently resolving concrete class name to detect infinite loop.
     *
     * @var array
     * @psalm-var array<class-string, boolean>
     */
    private array $resolvingConcretes = [];

    /**
     * Resolves concrete instance and instanciate that.
     *
     * @template T
     * @param class-string<T> $concrete
     * @param Container $container
     * @return T
     */
    public function resolve(string $concrete, Container $container): object
    {
        $ref = new ReflectionClass($concrete);

        if (!$ref->isInstantiable()) {
            throw new ReflectionException(sprintf(
                'Could not resolve class "%s", it must have concrete and public constructor',
                $concrete,
            ));
        }

        if (array_key_exists($concrete, $this->resolvingConcretes)) {
            throw new ReflectionException(sprintf(
                'Resolve infinite loop detected on "%s"',
                $concrete,
            ));
        }
        $this->resolvingConcretes[$concrete] = true;

        $constructor = $ref->getConstructor();
        if ($constructor === null) {
            // No constructor, it can make no args
            unset($this->resolvingConcretes[$concrete]);
            return $ref->newInstance();
        }

        $params = $constructor->getParameters();

        if (count($params) === 0) {
            // No parameter constructor
            unset($this->resolvingConcretes[$concrete]);
            return $ref->newInstance();
        }

        $resolved_params = [];
        foreach ($params as $param) {
            $resolved_params[$param->getName()] = $this->resolveParameter($param, $container);
        }

        unset($this->resolvingConcretes[$concrete]);
        return $ref->newInstanceArgs($resolved_params);
    }

    /**
     * Whether concrete class can resolve?
     *
     * @param string $concrete
     * @return bool
     */
    public function canResolve(string $concrete, Container $container): bool
    {
        /** @todo avoid try-catch in regular case */
        try {
            $this->resolve($concrete, $container);
            return true;
        } catch (ReflectionException $_) {
            return false;
        }
    }

    /**
     * Calls method with injection
     *
     * @template T
     * @param callable $func
     * @psalm-param callable(...mixed):T $func
     * @param Container $container
     * @param array $parameters
     * @return mixed
     * @psalm-return T
     */
    public function call(callable $func, Container $container, array $parameters = []): mixed
    {
        $ref = new ReflectionFunction(Closure::fromCallable($func));

        $params = $ref->getParameters();

        if (count($params) === 0) {
            // No parameter function
            return $ref->invoke();
        }

        $resolved_params = [];
        foreach ($params as $param) {
            $name = $param->getName();
            if (array_key_exists($name, $parameters)) {
                // prefer requested parameters
                $resolved_params[$name] = $parameters[$name];
            } else {
                $resolved_params[$name] = $this->resolveParameter($param, $container);
            }
        }

        return $ref->invokeArgs($resolved_params);
    }

    /**
     * Resolves one parameter
     *
     * @param ReflectionParameter $parameter
     * @param Container $container
     * @return object
     */
    private function resolveParameter(ReflectionParameter $parameter, Container $container): object
    {
        $type = $parameter->getType();
        if ($type === null) {
            throw new ReflectionException(sprintf('Could not resolve parameter that "%s" is null', $parameter->getName()));
        } elseif ($type instanceof ReflectionUnionType) {
            throw new ReflectionException(sprintf('Could not resolve parameter that "%s" is UnionType', $parameter->getName()));
        } elseif ($type instanceof ReflectionIntersectionType) {
            throw new ReflectionException(sprintf('Could not resolve parameter that "%s" is IntersectionType', $parameter->getName()));
        }

        $name = $type->getName();

        if (
            $name === 'bool' ||
            $name === 'int' ||
            $name === 'float' ||
            $name === 'double' ||
            $name === 'string' ||
            $name === 'array' ||
            $name === 'object' ||
            $name === 'callable' ||
            $name === 'iterable' ||
            $name === 'resource'
        ) {
            throw new ReflectionException(sprintf('Primitive type "%s" cannot resolve', $name));
        }

        return $container->get($name);
    }
}
