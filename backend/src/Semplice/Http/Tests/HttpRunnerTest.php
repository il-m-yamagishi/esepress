<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Http\Tests;

use Semplice\Contracts\Http\IHttpErrorHandler;
use Semplice\Contracts\Http\IHttpResponseEmitter;
use Semplice\Http\HttpRunner;
use PHPUnit\Framework\TestCase;
use Prophecy\Doubler\Generator\Node\ReturnTypeNode;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @coversDefaultClass \Semplice\Http\HttpRunner
 */
class HttpRunnerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     * @covers ::run
     */
    public function test_run(): void
    {
        $server_request = $this->prophesize(ServerRequestInterface::class);
        $response = $this->prophesize(ResponseInterface::class);

        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle($server_request->reveal())->shouldBeCalledOnce()->willReturn($response->reveal());

        $error_handler = $this->prophesize(IHttpErrorHandler::class);
        $error_handler->handleError($server_request->reveal())->shouldNotBeCalled();

        $emitter = $this->prophesize(IHttpResponseEmitter::class);
        $emitter->emit($response->reveal())->shouldBeCalledOnce();
        // @todo erase "never-returning function must not implicitly return" error
        $emitter->terminate()->shouldBeCalledOnce()->willReturn(new ReturnTypeNode('never'));

        $runner = new HttpRunner(
            $handler->reveal(),
            $error_handler->reveal(),
            $emitter->reveal(),
        );

        /** @todo */
        /** @psalm-suppress UnevaluatedCode */
        $this->markTestIncomplete('never return type is not yet supported?');

        $runner->run($server_request->reveal());
    }
}
