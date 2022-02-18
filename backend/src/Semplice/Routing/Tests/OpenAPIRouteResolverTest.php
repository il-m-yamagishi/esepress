<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Routing\Tests;

use LogicException;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Semplice\Contracts\Routing\MethodNotAllowedException;
use Semplice\Contracts\Routing\NotFoundException;
use Semplice\Routing\OpenAPIRouteResolver;

/**
 * @coversDefaultClass \Semplice\Routing\OpenAPIRouteResolver
 */
class OpenAPIRouteResolverTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function test_resolve(): void
    {
        $expected = 'this_is_invoker';
        $raw = [
            'paths' => [
                '/foo/bar' => [
                    'get' => [
                        'x-invoker' => $expected,
                    ],
                ],
            ],
        ];
        $uri = $this->prophesize(UriInterface::class);
        $uri->getPath()
            ->shouldBeCalledOnce()
            ->willReturn('/foo/bar');
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()
            ->shouldBeCalledOnce()
            ->willReturn($uri->reveal());
        $request->getMethod()
            ->shouldBeCalledOnce()
            ->willReturn('GET');

        $resolver = new OpenAPIRouteResolver($raw);
        $actual = $resolver->resolve($request->reveal());

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function test_not_found(): void
    {
        $raw = [
            'paths' => [
            ],
        ];
        $uri = $this->prophesize(UriInterface::class);
        $uri->getPath()
            ->shouldBeCalledOnce()
            ->willReturn('/foo');
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()
            ->shouldBeCalledOnce()
            ->willReturn($uri->reveal());
        $request->getMethod()
            ->shouldBeCalledOnce()
            ->willReturn('GET');

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Path "get /foo" not found');

        $resolver = new OpenAPIRouteResolver($raw);
        $resolver->resolve($request->reveal());
    }

    /**
     * @test
     */
    public function test_method_not_allowed(): void
    {
        $raw = [
            'paths' => [
                '/bar' => [
                    'get' => [
                    ],
                ],
            ],
        ];
        $uri = $this->prophesize(UriInterface::class);
        $uri->getPath()
            ->shouldBeCalledOnce()
            ->willReturn('/bar');
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()
            ->shouldBeCalledOnce()
            ->willReturn($uri->reveal());
        $request->getMethod()
            ->shouldBeCalledOnce()
            ->willReturn('POST');

        $this->expectException(MethodNotAllowedException::class);
        $this->expectExceptionMessage('Path "post /bar" method not allowed');

        $resolver = new OpenAPIRouteResolver($raw);
        $resolver->resolve($request->reveal());
    }

    /**
     * @test
     */
    public function test_invalid_schema(): void
    {
        $raw = [
            'paths' => [
                '/baz' => [
                    'put' => [
                    ],
                ],
            ],
        ];
        $uri = $this->prophesize(UriInterface::class);
        $uri->getPath()
            ->shouldBeCalledOnce()
            ->willReturn('/baz');
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()
            ->shouldBeCalledOnce()
            ->willReturn($uri->reveal());
        $request->getMethod()
            ->shouldBeCalledOnce()
            ->willReturn('PUT');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Invalid schema: paths./baz.put.x-invoker is not defined');

        $resolver = new OpenAPIRouteResolver($raw);
        $resolver->resolve($request->reveal());
    }
}
