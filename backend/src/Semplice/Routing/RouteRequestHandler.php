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
     * Undocumented function
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
        $invokerClassName = $this->resolver->resolve($request);
        $invokerInstance = $this->container->get($invokerClassName);
        assert(method_exists($invokerInstance, '__invoke'), 'Invoker must have ::__invoke method');

        $response = $this->container->call([$invokerInstance, '__invoke'], compact('request'));
        assert($response instanceof ResponseInterface);

        return $response;
    }
}
