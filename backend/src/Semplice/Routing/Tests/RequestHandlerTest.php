<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Routing\Tests;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Semplice\Contracts\Container\IContainer;
use Semplice\Contracts\Routing\IRouteResolver;
use Semplice\Routing\RouteRequestHandler;

/**
 * @coversDefaultClass \Semplice\Routing\RequestHandler
 */
class RequestHandlerTest extends TestCase
{
    use ProphecyTrait;

    public function test_handle(): void
    {
        $resolver = $this->prophesize(IRouteResolver::class);
        $container = $this->prophesize(IContainer::class);
        $request = $this->prophesize(ServerRequestInterface::class);
        $handler = new RouteRequestHandler($resolver->reveal(), $container->reveal());
        $response = $this->prophesize(ResponseInterface::class);
        $invoker = new class ($response->reveal()) {
            public function __construct(private readonly ResponseInterface $response)
            {
            }

            public function __invoke(ServerRequestInterface $request): ResponseInterface
            {
                return $this->response;
            }
        };

        $resolver->resolve($request->reveal())
            ->shouldBeCalledOnce()
            ->willReturn(get_class($invoker));
        $container->get(get_class($invoker))
            ->shouldBeCalledOnce()
            ->willReturn($invoker);
        $container->call($invoker, compact('request'))
            ->shouldBeCalledOnce()
            ->willReturn($response->reveal());

        $response = $handler->handle($request->reveal());

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
