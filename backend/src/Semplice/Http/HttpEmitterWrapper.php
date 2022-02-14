<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Http;

use function header;
use function headers_sent;
use function ob_get_length;
use function ob_get_level;

/**
 * global functions wrapper
 * @internal
 * @psalm-internal Semplice\Http
 */
class HttpEmitterWrapper
{
    /**
     * Header has sent or not
     *
     * @return boolean
     */
    public function hasSentHeader(): bool
    {
        return headers_sent();
    }

    /**
     * Body flushed or not
     *
     * @return boolean
     */
    public function hasObFlushed(): bool
    {
        return ob_get_level() > 0 && ob_get_length() > 0;
    }

    /**
     * Add header
     *
     * @param string $name
     * @param string $value
     * @param boolean $replace
     * @param integer $status_code
     * @return void
     */
    public function addHeader(string $name, string $value, bool $replace, int $status_code): void
    {
        $this->addHeaderRaw(
            $name . ': ' . $value,
            $replace,
            $status_code,
        );
    }

    /**
     * Add header raw
     *
     * @param string $line
     * @param boolean $replace
     * @param integer $status_code
     * @return void
     */
    public function addHeaderRaw(string $line, bool $replace, int $status_code): void
    {
        header(
            $line,
            $replace,
            $status_code,
        );
    }

    /**
     * Echo body string
     *
     * @param string $body
     * @return void
     */
    public function echoBody(string $body): void
    {
        echo $body;
    }
}
