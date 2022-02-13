<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache 2.0
 */

declare(strict_types=1);

namespace EsePress\Contracts\Http;

use Psr\Http\Message\ResponseInterface;

/**
 * Emit HTTP response headers and bodies.
 */
interface IHttpResponseEmitter
{
    /**
     * Emit http response headers and bodies.
     *
     * @param ResponseInterface $response
     * @return void
     */
    public function emit(ResponseInterface $response): void;

    /**
     * finish request and exit quietly.
     *
     * @return never
     */
    public function terminate(): never;
}
