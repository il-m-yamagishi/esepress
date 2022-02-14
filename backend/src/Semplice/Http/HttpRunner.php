<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Http;

use Semplice\Contracts\Http\IHttpErrorHandler;
use Semplice\Contracts\Http\IHttpResponseEmitter;
use Semplice\Contracts\Http\IHttpRunner;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

/**
 * @package \Semplice\Http
 */
class HttpRunner implements IHttpRunner
{
    public function __construct(
        private readonly RequestHandlerInterface $handler,
        private readonly IHttpErrorHandler $error_handler,
        private readonly IHttpResponseEmitter $emitter = new HttpResponseEmitter(new HttpEmitterWrapper()),
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function run(ServerRequestInterface $request): never
    {
        $response = null;
        try {
            $response = $this->handler->handle($request);
        } catch (Throwable $throwable) {
            $response = $this->error_handler->handleError($request, $throwable);
        } finally {
            assert($response instanceof ResponseInterface);
            $this->emitter->emit($response);
            $this->emitter->terminate();
        }
    }
}
