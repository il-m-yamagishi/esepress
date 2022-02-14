<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Routing\Tests;

use Semplice\Routing\Get;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use ReflectionMethod;

class GetTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function test_get(): void
    {
        $controller = new DummyGetController();

        $ref = new ReflectionMethod($controller, '__invoke');
        $attrs = $ref->getAttributes();
        $this->assertCount(1, $attrs);

        $get = $attrs[0]->newInstance();

        $this->assertInstanceOf(Get::class, $get);
        $this->assertSame('/dummy', $get->path);
    }
}

// phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
class DummyGetController
{
    #[Get('/dummy')]
    public function __invoke(): void
    {
    }
}
