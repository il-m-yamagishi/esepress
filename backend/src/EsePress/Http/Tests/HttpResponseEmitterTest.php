<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace EsePress\Http\Tests;

use EsePress\Http\HttpEmitterWrapper;
use EsePress\Http\HttpResponseEmitter;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;

/**
 * @coversDefaultClass \EsePress\Http\HttpResponseEmitter
 */
class HttpResponseEmitterTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     * @covers ::emit
     * @covers ::validateCanEmit
     * @covers ::emitHeaders
     * @covers ::emitStatusLine
     * @covers ::emitBody
     */
    public function test_emit(): void
    {
        $response = $this->prophesize(ResponseInterface::class);
        $response->getStatusCode()->shouldBeCalledOnce()->willReturn(200);
        $response->getHeaders()->shouldBeCalledOnce()->willReturn([
            'Set-Cookie' => ['a', 'b'],
            'Content-Type' => ['application/json'],
            'content-length' => ['2'],
        ]);
        $response->getProtocolVersion()->shouldBeCalledOnce()->willReturn('1.1');
        $response->getReasonPhrase()->shouldBeCalledOnce()->willReturn('OK');
        $response->getStatusCode()->shouldBeCalled()->willReturn(200);
        $response->getBody()->shouldBeCalledOnce()->willReturn('{}');

        $wrapper = $this->prophesize(HttpEmitterWrapper::class);
        $wrapper->hasSentHeader()->shouldBeCalledOnce()->willReturn(false);
        $wrapper->hasObFlushed()->shouldBeCalledOnce()->willReturn(false);
        $wrapper->addHeader('Set-Cookie', 'a', false, 200)->shouldBeCalledOnce();
        $wrapper->addHeader('Set-Cookie', 'b', false, 200)->shouldBeCalledOnce();
        $wrapper->addHeader('Content-Type', 'application/json', true, 200)->shouldBeCalledOnce();
        $wrapper->addHeader('Content-Length', 2, true, 200)->shouldBeCalledOnce();
        $wrapper->addHeaderRaw('HTTP/1.1 200 OK', true, 200)->shouldBeCalledOnce();
        $wrapper->echoBody('{}')->shouldBeCalledOnce();

        $emitter = new HttpResponseEmitter($wrapper->reveal());
        $emitter->emit($response->reveal());
    }
}
