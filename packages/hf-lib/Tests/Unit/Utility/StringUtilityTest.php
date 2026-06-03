<?php

declare(strict_types=1);

namespace Hausformat\Lib\Tests\Unit\Utility;

use Hausformat\Lib\Utility\StringUtility;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class StringUtilityTest extends UnitTestCase
{
    #[Test]
    public function sanitizeUmlautsConvertsGermanUmlautsToAscii(): void
    {
        $this->assertSame('Haeuser', StringUtility::sanitizeUmlauts('Häuser'));
    }

    #[Test]
    public function sanitizeUmlautsLeavesAsciiStringUnchanged(): void
    {
        $this->assertSame('Hello World', StringUtility::sanitizeUmlauts('Hello World'));
    }

    #[Test]
    public function sanitizeUmlautsConvertsAllGermanVowels(): void
    {
        $result = StringUtility::sanitizeUmlauts('äöüÄÖÜ');

        $this->assertSame('aeoeueAeOeUe', $result);
    }

    #[Test]
    public function sanitizeSpecialCharsRemovesExclamationMark(): void
    {
        $result = StringUtility::sanitizeSpecialChars('hello!world');

        $this->assertStringNotContainsString('!', $result);
    }

    #[Test]
    public function sanitizePathReplacesSpacesWithDefaultUnderscore(): void
    {
        $this->assertSame('hello_world', StringUtility::sanitizePath('hello world'));
    }

    #[Test]
    public function sanitizePathUsesCustomWhitespaceChar(): void
    {
        $this->assertSame('hello-world', StringUtility::sanitizePath('hello world', '-'));
    }

    #[Test]
    public function sanitizePathRemovesAtSign(): void
    {
        $result = StringUtility::sanitizePath('hello@world');

        $this->assertStringNotContainsString('@', $result);
    }

    #[Test]
    public function getTypeReturnsStringForEmptyInput(): void
    {
        $this->assertSame('string', StringUtility::getType(''));
    }

    #[Test]
    public function getTypeDetectsEmailAddress(): void
    {
        $this->assertSame('email', StringUtility::getType('user@example.com'));
    }

    #[Test]
    public function getTypeDetectsIntegerValue(): void
    {
        $this->assertSame('int', StringUtility::getType('42'));
    }

    #[Test]
    public function getTypeReturnsStringForPlainText(): void
    {
        $this->assertSame('string', StringUtility::getType('just some text'));
    }

    #[Test]
    public function clearWhiteSpacesRemovesUnixLinebreaks(): void
    {
        $this->assertSame('helloworld', StringUtility::clearWhiteSpaces("hello\nworld"));
    }

    #[Test]
    public function clearWhiteSpacesRemovesWindowsLinebreaks(): void
    {
        $this->assertSame('helloworld', StringUtility::clearWhiteSpaces("hello\r\nworld"));
    }

    #[Test]
    public function clearWhiteSpacesRemovesCarriageReturn(): void
    {
        $this->assertSame('helloworld', StringUtility::clearWhiteSpaces("hello\rworld"));
    }

    #[Test]
    public function toStringReturnsStringAsIs(): void
    {
        $this->assertSame('hello', StringUtility::toString('hello'));
    }

    #[Test]
    public function toStringConvertsArrayToJsonString(): void
    {
        $this->assertSame('[1,2,3]', StringUtility::toString([1, 2, 3]));
    }

    #[Test]
    public function toStringConvertsIntegerToString(): void
    {
        $this->assertSame('42', StringUtility::toString(42));
    }
}
