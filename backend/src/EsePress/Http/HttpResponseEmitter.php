<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace EsePress\Http;

use EsePress\Contracts\Http\IHttpResponseEmitter;
use Psr\Http\Message\ResponseInterface;

use function assert;
use function fastcgi_finish_request;
use function function_exists;
use function is_string;
use function ucwords;

class HttpResponseEmitter implements IHttpResponseEmitter
{
    public function __construct(
        private readonly HttpEmitterWrapper $wrapper,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function emit(ResponseInterface $response): void
    {
        $this->validateCanEmit();
        $this->emitHeaders($response);
        $this->emitStatusLine($response);
        $this->emitBody($response);
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public function terminate(): never
    {
        assert($this->wrapper->hasSentHeader());

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        exit(0);
    }

    /**
     * Validates no header sent and no ob_flush.
     * @throws EmitException
     * @return void
     */
    private function validateCanEmit(): void
    {
        if ($this->wrapper->hasSentHeader()) {
            throw new EmitException(sprintf(
                'It could not emit after headers has sent',
            ));
        }

        if ($this->wrapper->hasObFlushed()) {
            throw new EmitException(
                'It could not emit after ob has sent',
            );
        }
    }

    /**
     * Emit all headers except Status Line
     *
     * @param ResponseInterface $response
     * @return void
     */
    private function emitHeaders(ResponseInterface $response): void
    {
        $status_code = $response->getStatusCode();

        foreach ($response->getHeaders() as $name => $values) {
            assert(is_string($name));
            $name = ucwords($name, '-');
            $replace = $name !== 'Set-Cookie';
            foreach ($values as $value) {
                $this->wrapper->addHeader(
                    $name,
                    $value,
                    $replace,
                    $status_code,
                );
                $replace = false;
            }
        }
    }

    /**
     * Emit first status line
     *
     * @param ResponseInterface $response
     * @return void
     */
    private function emitStatusLine(ResponseInterface $response): void
    {
        $protocol_version = $response->getProtocolVersion();
        $reason_phrase = $response->getReasonPhrase();
        $status_code = $response->getStatusCode();

        $this->wrapper->addHeaderRaw(sprintf(
            'HTTP/%s %d%s',
            $protocol_version,
            $status_code,
            $reason_phrase ? ' ' . $reason_phrase : '',
        ), true, $status_code);
    }

    /**
     * Emit body content
     *
     * @param ResponseInterface $response
     * @return void
     */
    private function emitBody(ResponseInterface $response): void
    {
        $this->wrapper->echoBody((string)$response->getBody());
    }
}
