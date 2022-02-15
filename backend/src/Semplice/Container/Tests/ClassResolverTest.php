<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Container\Tests;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use ReflectionException;
use Semplice\Container\ClassResolver;
use Semplice\Container\Container;

class ClassResolverTest extends TestCase
{
    use ProphecyTrait;

    public function dataProviderReflectionExceptions(): array
    {
        return [
            'private class' => [
                'Could not resolve class "Semplice\Container\Tests\ClassResolverPrivateClass", it must have concrete and public constructor',
                ClassResolverPrivateClass::class,
            ],
            'protected class' => [
                'Could not resolve class "Semplice\Container\Tests\ClassResolverProtectedClass", it must have concrete and public constructor',
                ClassResolverProtectedClass::class,
            ],
            'abstract class' => [
                'Could not resolve class "Semplice\Container\Tests\ClassResolverAbstractClass", it must have concrete and public constructor',
                ClassResolverAbstractClass::class,
            ],
            'unknown class' => [
                'Class "UnknownClass" does not exist',
                'UnknownClass',
            ],
            'interface' => [
                'Could not resolve class "Semplice\Container\Tests\ClassResolverInterface", it must have concrete and public constructor',
                ClassResolverInterface::class,
            ],
            'trait' => [
                'Could not resolve class "Semplice\Container\Tests\ClassResolverTrait", it must have concrete and public constructor',
                ClassResolverTrait::class,
            ],
            'null' => [
                'Could not resolve parameter that "nullable" is null',
                ClassResolverNullClass::class,
            ],
            'union' => [
                'Could not resolve parameter that "union" is UnionType',
                ClassResolverUnionTypeClass::class,
            ],
            'intersection' => [
                'Could not resolve parameter that "intersection" is IntersectionType',
                ClassResolverIntersectionTypeClass::class,
            ],
            'concrete enum' => [
                'Could not resolve class "Semplice\Container\Tests\ClassResolverEnum", it must have concrete and public constructor',
                ClassResolverEnum::class,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider dataProviderReflectionExceptions
     * @param string $exceptionMessage
     * @param string $concreteClass
     * @psalm-param class-string $concreteClass
     * @return void
     */
    public function test_try_to_resolve_unresolvable_class(
        string $exceptionMessage,
        string $concreteClass,
    ): void {
        $container = $this->prophesize(Container::class);
        $this->expectException(ReflectionException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $resolver = new ClassResolver();
        $resolver->resolve($concreteClass, $container->reveal());
    }

    /**
     * @test
     */
    public function test_resolve_simple_no_constructor_class(): void
    {
        $container = $this->prophesize(Container::class);
        $resolver = new ClassResolver();

        $expectedClass = ClassResolverUndefinedConstructClass::class;
        $actual = $resolver->resolve($expectedClass, $container->reveal());

        $this->assertInstanceOf($expectedClass, $actual);
    }

    /**
     * @test
     */
    public function test_resolve_simple_no_parameters_class(): void
    {
        $container = $this->prophesize(Container::class);
        $resolver = new ClassResolver();

        $expectedClass = ClassResolverNoParametersClass::class;
        $actual = $resolver->resolve($expectedClass, $container->reveal());

        $this->assertInstanceOf($expectedClass, $actual);
    }

    /**
     * @test
     */
    public function test_resolve_causes_infinite_loop(): void
    {
        $this->expectException(ReflectionException::class);
        $this->expectExceptionMessage('Resolve infinite loop detected on "Semplice\Container\Tests\ClassResolverInfiniteLoopClass"');

        $resolver = new ClassResolver();
        // depends on concrete class to reproduce infinite loop
        $container = new Container($resolver);
        $resolver->resolve(ClassResolverInfiniteLoopClass::class, $container);
    }

    /**
     * @test
     */
    public function test_resolve_recursive(): void
    {
        $resolver = new ClassResolver();
        // depends on concrete class to reproduce recursive get
        $container = new Container($resolver);
        $container->instance(ClassResolverA::class, new ClassResolverA('config_test'));

        $actual = $resolver->resolve(ClassResolverD::class, $container);

        $this->assertInstanceOf(ClassResolverD::class, $actual);
    }

    /**
     * @test
     */
    public function test_resolve_recursive_causes_exception(): void
    {
        $resolver = new ClassResolver();
        // depends on concrete class to reproduce recursive get
        $container = new Container($resolver);

        // ClassResolverA won't resolve
        // $container->instance(ClassResolverA::class, new ClassResolverA('config_test'));

        $this->expectException(ReflectionException::class);
        $this->expectExceptionMessage('Primitive type "string" cannot resolve');

        $resolver->resolve(ClassResolverD::class, $container);
    }
}

// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses

class ClassResolverPrivateClass
{
    private function __construct()
    {
    }
}

class ClassResolverProtectedClass
{
    protected function __construct()
    {
    }
}

abstract class ClassResolverAbstractClass
{
}

interface ClassResolverInterface
{
}

trait ClassResolverTrait
{
}

class ClassResolverUndefinedConstructClass
{
}

class ClassResolverNoParametersClass
{
    public function __construct()
    {
    }
}

class ClassResolverNullClass
{
    public function __construct($nullable)
    {
    }
}

class ClassResolverUnionTypeClass
{
    public function __construct(ClassResolverUndefinedConstructClass|ClassResolverNoParametersClass $union)
    {
    }
}

class ClassResolverIntersectionTypeClass
{
    public function __construct(ClassResolverUndefinedConstructClass&ClassResolverNoParametersClass $intersection)
    {
    }
}

enum ClassResolverEnum
{
}

class ClassResolverEnumTypeClass
{
    public function __construct(ClassResolverEnum $enum)
    {
    }
}

class ClassResolverInfiniteLoopClass
{
    public function __construct(ClassResolverInfiniteLoopClass $infinite_loop)
    {
    }
}

class ClassResolverA
{
    public function __construct(string $config)
    {
    }
}

class ClassResolverB
{
    public function __construct(ClassResolverA $a)
    {
    }
}

class ClassResolverC
{
    public function __construct(ClassResolverB $b, ClassResolverA $a)
    {
    }
}

class ClassResolverD
{
    public function __construct(ClassResolverB $b, ClassResolver $c)
    {
    }
}
