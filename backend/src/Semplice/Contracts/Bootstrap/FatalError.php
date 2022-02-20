<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Contracts\Bootstrap;

use Error;

/**
 * Application has fired fatal error
 */
class FatalError extends Error
{
    /**
     * Constructor
     *
     * @param array{type:int, message:string, file: string, line: int} $last_error
     */
    public function __construct(array $last_error)
    {
        parent::__construct($last_error['message'], $last_error['type']);
        $this->file = $last_error['file'];
        $this->line = $last_error['line'];
    }
}
