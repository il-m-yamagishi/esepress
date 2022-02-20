<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Semplice\Contracts\Container\IContainer;
use Semplice\Contracts\Routing\IRouteResolver;

/**
 * Resolved routing request handler
 */
class RouteRequestHandler implements RequestHandlerInterface
{
    /**
     * Constructor
     *
     * @param IRouteResolver $resolver
     * @param IContainer $container
     */
    public function __construct(
        private readonly IRouteResolver $resolver,
        private readonly IContainer $container,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $invoker_class_name = $this->resolver->resolve($request);
        $invoker_instance = $this->container->get($invoker_class_name);
        assert(is_callable($invoker_instance), sprintf('Ensure invoker %s is callable', $invoker_class_name));

        $response = $this->container->call($invoker_instance, compact('request'));
        assert($response instanceof ResponseInterface);

        return $response;
    }
}
