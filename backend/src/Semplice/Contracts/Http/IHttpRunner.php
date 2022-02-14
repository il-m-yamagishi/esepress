<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Contracts\Http;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Run mainstream logic with ServerRequest and emit, then exit
 */
interface IHttpRunner
{
    /**
     * Run application.
     *
     * @param ServerRequestInterface $request
     * @return never no return, just exit
     */
    public function run(ServerRequestInterface $request): never;
}
