<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Routing;

use Attribute;

/**
 * The attribute has appending PSR-15 middleware name
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_FUNCTION | Attribute::IS_REPEATABLE)]
final class Middleware
{
    /**
     * Constructor
     *
     * @param string $name class name of middleware
     */
    public function __construct(
        public readonly string $name,
    ) {
    }
}
