<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Http;

use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Production environment error handler
 */
class ProductionHttpErrorHandler extends DevelopmentHttpErrorHandler
{
    /**
     * {@inheritDoc}
     */
    protected function renderJson(Throwable $throwable): ResponseInterface
    {
        $body = [
            'message' => $throwable->getMessage(),
        ];
        $body_raw = json_encode($body, JSON_THROW_ON_ERROR);

        return $this->response_factory->createResponse(500, 'Internal server error')
            ->withBody($this->stream_factory->createStream($body_raw))
            ->withHeader('Content-Type', 'application/json; UTF-8')
            ->withHeader('Content-Length', strlen($body_raw));
    }
}
