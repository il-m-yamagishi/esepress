<?php // phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols

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
use Semplice\Contracts\Container\CouldNotBeResolvedException;

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
     * @param string $exception_message
     * @param string $concrete_class
     * @psalm-param class-string $concrete_class
     * @return void
     */
    public function test_try_to_resolve_unresolvable_class(
        string $exception_message,
        string $concrete_class,
    ): void {
        $container = $this->prophesize(Container::class);
        $this->expectException(ReflectionException::class);
        $this->expectExceptionMessage($exception_message);

        $resolver = new ClassResolver();
        $resolver->resolve($concrete_class, $container->reveal());
    }

    /**
     * @test
     */
    public function test_resolve_simple_no_constructor_class(): void
    {
        $container = $this->prophesize(Container::class);
        $resolver = new ClassResolver();

        $expected_class = ClassResolverUndefinedConstructClass::class;
        $actual = $resolver->resolve($expected_class, $container->reveal());

        $this->assertInstanceOf($expected_class, $actual);
    }

    /**
     * @test
     */
    public function test_resolve_simple_no_parameters_class(): void
    {
        $container = $this->prophesize(Container::class);
        $resolver = new ClassResolver();

        $expected_class = ClassResolverNoParametersClass::class;
        $actual = $resolver->resolve($expected_class, $container->reveal());

        $this->assertInstanceOf($expected_class, $actual);
    }

    /**
     * @test
     */
    public function test_resolve_causes_infinite_loop(): void
    {
        $this->expectException(CouldNotBeResolvedException::class);
        $this->expectExceptionMessage('"Semplice\Container\Tests\ClassResolverInfiniteLoopClass" could not be resolved because: Resolving loop detected on "Semplice\Container\Tests\ClassResolverInfiniteLoopClass"');

        $resolver = new ClassResolver();
        // depends on concrete class to reproduce infinite loop
        $container = new Container(resolver: $resolver);
        $resolver->resolve(ClassResolverInfiniteLoopClass::class, $container);
    }

    /**
     * @test
     */
    public function test_resolve_recursive(): void
    {
        $resolver = new ClassResolver();
        // depends on concrete class to reproduce recursive get
        $container = new Container(resolver: $resolver);
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
        $container = new Container(resolver: $resolver);

        // ClassResolverA won't resolve
        // $container->instance(ClassResolverA::class, new ClassResolverA('config_test'));

        $this->expectException(CouldNotBeResolvedException::class);
        $this->expectExceptionMessage('"Semplice\Container\Tests\ClassResolverA" could not be resolved because: Primitive type "string" cannot resolve');

        $resolver->resolve(ClassResolverD::class, $container);
    }

    /**
     * @test
     */
    public function test_call_function(): void
    {
        $resolver = new ClassResolver();
        // depends on concrete class to reproduce recursive get
        $container = new Container(resolver: $resolver);

        $a = new ClassResolverA('config_test');
        $container->instance(ClassResolverA::class, $a);

        $func = fn (ClassResolverB $b) => true;

        $actual = $resolver->call($func, $container, compact('a'));

        $this->assertTrue($actual);
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
