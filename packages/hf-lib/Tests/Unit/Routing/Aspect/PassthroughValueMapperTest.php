<?php

declare(strict_types=1);

namespace Hausformat\Lib\Tests\Unit\Routing\Aspect;

use Hausformat\Lib\Routing\Aspect\PassthroughValueMapper;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class PassthroughValueMapperTest extends UnitTestCase
{
    #[Test]
    public function generateReturnsValueWithSpacesReplacedByUnderscores(): void
    {
        $subject = new PassthroughValueMapper();

        $this->assertSame('hello_world', $subject->generate('hello world'));
    }

    #[Test]
    public function generateLeavesValueWithoutSpacesUnchanged(): void
    {
        $subject = new PassthroughValueMapper();

        $this->assertSame('hello-world', $subject->generate('hello-world'));
    }

    #[Test]
    public function generateConvertsToLowercaseWhenOptionEnabled(): void
    {
        $subject = new PassthroughValueMapper(['lowercase' => true]);

        $this->assertSame('hello_world', $subject->generate('HELLO WORLD'));
    }

    #[Test]
    public function generateSanitizesUmlautsWhenSanitizePathEnabled(): void
    {
        $subject = new PassthroughValueMapper(['sanitizePath' => true]);

        $result = $subject->generate('Häuser Wörter');

        $this->assertStringNotContainsString('ä', $result);
        $this->assertStringNotContainsString('ö', $result);
    }

    #[Test]
    public function generateAppliesBothLowercaseAndSanitizePath(): void
    {
        $subject = new PassthroughValueMapper(['lowercase' => true, 'sanitizePath' => true]);

        $result = $subject->generate('Häuser');

        $this->assertSame(strtolower($result), $result);
        $this->assertStringNotContainsString('ä', $result);
    }

    #[Test]
    public function resolveReturnsInputValueAsIs(): void
    {
        $subject = new PassthroughValueMapper();

        $this->assertSame('some-slug', $subject->resolve('some-slug'));
    }

    #[Test]
    public function resolvePreservesEmptyString(): void
    {
        $subject = new PassthroughValueMapper();

        $this->assertSame('', $subject->resolve(''));
    }
}
