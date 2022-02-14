<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Http;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use SplQueue;

/**
 * Processes multiple RequestHandlerInterface and MiddlewareInterface
 * Logics is inspired by [Relay](https://github.com/relayphp/Relay.Relay).
 */
class RequestHandlerPipeline implements RequestHandlerInterface
{
    /** @var SplQueue<class-string> $queue */
    private SplQueue $queue;
    private Closure $resolver;

    /**
     * Constructor
     *
     * @param class-string[] $handlers
     * @param Closure $resolver instance resolver
     */
    public function __construct(
        array $handlers,
        Closure $resolver,
    ) {
        /** @var SplQueue<class-string> */
        $this->queue = new SplQueue();
        $this->resolver = $resolver;

        foreach ($handlers as $handler) {
            $this->queue->enqueue($handler);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $handler = $this->queue->dequeue();
        $resolver = $this->resolver;
        $resolved_handler = $resolver($handler);
        assert($resolved_handler instanceof RequestHandlerInterface || $resolved_handler instanceof MiddlewareInterface);

        if ($resolved_handler instanceof MiddlewareInterface) {
            return $resolved_handler->process($request, $this);
        } elseif ($resolved_handler instanceof RequestHandlerInterface) {
            return $resolved_handler->handle($request);
        }

        throw new RuntimeException(sprintf(
            'Unknown handler: %s. Handler must be instance of MiddlewareInterface or RequestHandlerInterface.',
            $handler,
        ));
    }
}
