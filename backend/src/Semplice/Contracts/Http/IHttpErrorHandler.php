<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Contracts\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

/**
 * Processes any throwable error
 */
interface IHttpErrorHandler
{
    /**
     * Processes error and convert to ResponseInterface.
     *
     * @param ServerRequestInterface $request
     * @param Throwable $throwable
     * @return ResponseInterface
     */
    public function handleError(ServerRequestInterface $request, Throwable $throwable): ResponseInterface;
}
