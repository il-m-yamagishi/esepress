<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Container;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * @internal
 * @psalm-internal \Semplice\Container
 */
class ClassResolver
{
    public function __construct(
        private readonly Container $container,
    ) {
    }

    /**
     * Resolves concrete instance and instanciate that.
     *
     * @param class-string $concrete
     * @return object
     */
    public function resolve(string $concrete): object
    {
        $ref = new ReflectionClass($concrete);

        if (!$ref->isInstantiable()) {
            throw new ReflectionException(sprintf('concrete %s cannot be instanciated', $concrete));
        }

        $constructor = $ref->getConstructor();
        if ($constructor === null) {
            // No constructor, it can make no args
            return $ref->newInstance();
        }

        /** @todo implementation */
        throw new \LogicException('Not implemented yet');
    }

    /**
     * Whether concrete class can resolve?
     *
     * @param string $concrete
     * @return boolean
     */
    public function canResolve(string $concrete): bool
    {
        $ref = new ReflectionClass($concrete);

        if (!$ref->isInstantiable()) {
            return false;
        }
        return true;
    }

    /**
     * Resolves instance specific method.
     *
     * @param object $instance
     * @param string $method_name
     * @return mixed
     */
    public function resolveMethod(object $instance, string $method_name): mixed
    {
        /** @todo implementation */
        throw new \LogicException('Not implemented yet');
    }
}
