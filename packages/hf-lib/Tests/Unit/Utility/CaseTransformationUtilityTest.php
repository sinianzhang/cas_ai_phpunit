<?php

declare(strict_types=1);

namespace Hausformat\Lib\Tests\Unit\Utility;

use Hausformat\Lib\Utility\CaseTransformationUtility;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class CaseTransformationUtilityTest extends UnitTestCase
{
    #[Test]
    public function transformConvertsToLowercase(): void
    {
        $this->assertSame('hello world', CaseTransformationUtility::transform('HELLO WORLD', 'lowercase'));
    }

    #[Test]
    public function transformConvertsToUppercase(): void
    {
        $this->assertSame('HELLO WORLD', CaseTransformationUtility::transform('hello world', 'uppercase'));
    }

    #[Test]
    public function transformConvertsToPascalCase(): void
    {
        $this->assertSame('HelloWorld', CaseTransformationUtility::transform('hello_world', 'PascalCase'));
    }

    #[Test]
    public function transformConvertsToCamelCase(): void
    {
        $this->assertSame('helloWorld', CaseTransformationUtility::transform('hello_world', 'camelCase'));
    }

    #[Test]
    public function transformConvertsToSnakeCase(): void
    {
        $this->assertSame('hello_world', CaseTransformationUtility::transform('helloWorld', 'snake_case'));
    }

    #[Test]
    public function transformConvertsToScreamingSnakeCase(): void
    {
        $this->assertSame('HELLO_WORLD', CaseTransformationUtility::transform('helloWorld', 'SCREAMING_SNAKE_CASE'));
    }

    #[Test]
    public function transformIsCaseInsensitiveForCaseArgument(): void
    {
        $this->assertSame('hello', CaseTransformationUtility::transform('HELLO', 'LOWER'));
    }

    #[Test]
    public function transformThrowsInvalidArgumentExceptionForUnknownCase(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CaseTransformationUtility::transform('hello', 'nonExistentCase');
    }

    #[Test]
    public function transformToPascalCaseConvertsUnderscoredInput(): void
    {
        $this->assertSame('FooBarBaz', CaseTransformationUtility::transformToPascalCase('foo_bar_baz'));
    }

    #[Test]
    public function transformToCamelCaseConvertsUnderscoredInput(): void
    {
        $this->assertSame('fooBarBaz', CaseTransformationUtility::transformToCamelCase('foo_bar_baz'));
    }

    #[Test]
    public function transformToSnakeCaseConvertsCamelCaseInput(): void
    {
        $this->assertSame('foo_bar_baz', CaseTransformationUtility::transformToSnakeCase('fooBarBaz'));
    }

    #[Test]
    public function transformToScreamingSnakeCaseConvertsCamelCaseInput(): void
    {
        $this->assertSame('FOO_BAR_BAZ', CaseTransformationUtility::transformToScreamingSnakeCase('fooBarBaz'));
    }
}
