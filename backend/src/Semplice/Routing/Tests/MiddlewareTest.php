<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Routing\Tests;

use Semplice\Routing\Middleware;
use Laminas\Diactoros\Response;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionMethod;

class MiddlewareTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function test_middleware(): void
    {
        $controller = new DummyMiddlewareController();

        $ref = new ReflectionMethod($controller, '__invoke');
        $attrs = $ref->getAttributes();
        $this->assertCount(2, $attrs);

        $middleware1 = $attrs[0]->newInstance();
        $middleware2 = $attrs[1]->newInstance();

        $this->assertInstanceOf(Middleware::class, $middleware1);
        $this->assertSame(DummyMiddleware::class, $middleware1->name);
        $this->assertInstanceOf(Middleware::class, $middleware2);
        $this->assertSame(DummyMiddleware2::class, $middleware2->name);
    }
}

// phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
class DummyMiddlewareController
{
    #[Middleware(\Semplice\Routing\Tests\DummyMiddleware::class)]
    #[Middleware(\Semplice\Routing\Tests\DummyMiddleware2::class)]
    public function __invoke(): void
    {
    }
}

// phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
class DummyMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return new Response();
    }
}

// phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
class DummyMiddleware2 implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return new Response();
    }
}
