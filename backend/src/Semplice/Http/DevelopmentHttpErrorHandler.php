<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Http;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Semplice\Contracts\Http\IHttpErrorHandler;
use Throwable;

/**
 * Development environment error handler
 */
class DevelopmentHttpErrorHandler implements IHttpErrorHandler
{
    public function __construct(
        private readonly ResponseFactoryInterface $response_factory,
        private readonly StreamFactoryInterface $stream_factory,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Logging exception to file, SaaS, etc
     *
     * @param ServerRequestInterface $request
     * @param Throwable $throwable
     * @return void
     */
    protected function loggingError(ServerRequestInterface $request, Throwable $throwable): void
    {
        /** @todo logging by throwable class */
        $this->logger->error('Uncaught Exception: ' . $throwable->getMessage(), ['exception' => $throwable]);
    }

    /**
     * Render response
     *
     * @param ServerRequestInterface $request
     * @param Throwable $throwable
     * @return ResponseInterface
     */
    protected function renderError(ServerRequestInterface $request, Throwable $throwable): ResponseInterface
    {
        $accept = $request->getHeaderLine('Accept');

        if ($accept === '' || str_starts_with($accept, 'application/json')) {
            return $this->renderJson($throwable);
        }
        /** @todo render html? */
        return $this->renderJson($throwable);
    }

    protected function renderJson(Throwable $throwable): ResponseInterface
    {
        $body = [
            'message' => $throwable->getMessage(),
            'code' => $throwable->getCode(),
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'trace' => $throwable->getTrace(),
        ];
        $body_raw = json_encode($body, JSON_THROW_ON_ERROR);

        return $this->response_factory->createResponse(500, 'Internal server error')
            ->withBody($this->stream_factory->createStream($body_raw))
            ->withHeader('Content-Type', 'application/json; UTF-8')
            ->withHeader('Content-Length', strlen($body_raw));
    }

    /**
     * Handles errors on Http handler pipeline
     *
     * @param ServerRequestInterface $request
     * @param Throwable $throwable
     * @return ResponseInterface
     */
    public function handleError(ServerRequestInterface $request, Throwable $throwable): ResponseInterface
    {
        $this->loggingError($request, $throwable);

        return $this->renderError($request, $throwable);
    }
}
