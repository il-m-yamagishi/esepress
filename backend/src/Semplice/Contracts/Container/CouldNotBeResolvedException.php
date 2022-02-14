<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Contracts\Container;

use LogicException;

/**
 * The class could not be resolved.
 */
class CouldNotBeResolvedException extends LogicException
{
    /**
     * Constructor
     *
     * @param string $class_name Unresolved class name
     */
    public function __construct(string $class_name)
    {
        parent::__construct(sprintf('class %s could not be resolved', $class_name));
    }
}
