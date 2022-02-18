<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Contracts\Routing;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Resolves routing by ServerRequestInterface.
 */
interface IRouteResolver
{
    /**
     * Resolves routing and returns invoker class-string, or throw error
     *
     * @param ServerRequestInterface $request
     * @return string
     * @psalm-return class-string
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     */
    public function resolve(ServerRequestInterface $request): string;
}
