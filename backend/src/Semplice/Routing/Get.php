<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Routing;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_FUNCTION)]
final class Get
{
    /** @var string $path indicates endpoint path starts with / */
    public readonly string $path;

    /**
     * Constructor
     *
     * @param string $path indicates endpoint path starts with /
     */
    public function __construct(
        string $path,
    ) {
        $this->path = '/' . trim($path, '/');
    }
}
