<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace EsePress\Http\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ContentLengthAwareMiddleware implements MiddlewareInterface
{
    private const HEADER_NAME = 'Content-Length';

    /**
     * Ensure Content-Length header exists
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if ($response->hasHeader(self::HEADER_NAME)) {
            // noop
            return $response;
        }

        return $response->withHeader(self::HEADER_NAME, (string)$response->getBody()->getSize());
    }
}
