<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Contracts\Container;

use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use RuntimeException;

/**
 * The class could not be resolved.
 */
class CouldNotBeResolvedException extends RuntimeException implements NotFoundExceptionInterface
{
    /**
     * Constructor
     *
     * @param string $id Trying to resolve
     * @param ReflectionException $previous
     */
    public function __construct(string $id, ReflectionException $previous)
    {
        parent::__construct(sprintf('"%1$s" could not be resolved because: %2$s', $id, $previous->getMessage()), 0, $previous);
    }
}
