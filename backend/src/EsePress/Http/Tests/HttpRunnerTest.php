<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace EsePress\Http\Tests;

use EsePress\Contracts\Http\IHttpErrorHandler;
use EsePress\Contracts\Http\IHttpResponseEmitter;
use EsePress\Http\HttpRunner;
use PHPUnit\Framework\TestCase;
use Prophecy\Doubler\Generator\Node\ReturnTypeNode;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Promise\ReturnArgumentPromise;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @coversDefaultClass \EsePress\Http\HttpRunner
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
        $this->markTestIncomplete('never return type is not supported?');

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

        $runner->run($server_request->reveal());
    }
}
