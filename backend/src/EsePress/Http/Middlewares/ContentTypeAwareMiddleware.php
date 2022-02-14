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
use RuntimeException;

class ContentLengthAwareMiddleware implements MiddlewareInterface
{
    private const HEADER_NAME = 'Content-Type';

    /**
     * Ensure Content-Type is valid
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if ($response->hasHeader(self::HEADER_NAME)) {
            // noop
            return $response;
        }

        $body = (string)$response->getBody();

        if (str_starts_with($body, '{') || str_starts_with($body, '[')) {
            return $response->withHeader(self::HEADER_NAME, 'application/json');
        }

        throw new RuntimeException('Please set Content-Type');
    }
}
