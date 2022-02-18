<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Semplice\Contracts\Routing\IRouteResolver;
use Semplice\Contracts\Routing\MethodNotAllowedException;
use Semplice\Contracts\Routing\NotFoundException;

class OpenAPIRouteResolver implements IRouteResolver
{
    public function __construct(
        private readonly array $raw,
    ) {
        assert(array_key_exists('paths', $this->raw));
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(ServerRequestInterface $request): string
    {
        $uri = $request->getUri();
        $method = strtolower($request->getMethod());
        $path = $uri->getPath();

        /** @todo Supports path templating */

        if (!array_key_exists($path, $this->raw['paths'])) {
            throw new NotFoundException(sprintf(
                'Path "%s %s" not found',
                $method,
                $path,
            ));
        }

        $path_item_object = $this->raw['paths'][$path];
        if (!array_key_exists($method, $path_item_object)) {
            throw new MethodNotAllowedException(sprintf(
                'Path "%s %s" method not allowed',
                $method,
                $path,
            ));
        }

        $operation_object = $this->raw['paths'][$path][$method];
        if (!array_key_exists('x-invoker', $operation_object)) {
            throw new \LogicException(sprintf(
                'Invalid schema: paths.%s.%s.x-invoker is not defined',
                $path,
                $method,
            ));
        }

        $invoker = $operation_object['x-invoker'];

        assert(is_string($invoker));

        return $invoker;
    }
}
