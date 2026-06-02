<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\Dummy;

use Hausformat\ViewHelpers\Dummy\Email;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class EmailTest extends UnitTestCase
{
    #[Test]
    public function fromStringReturnsEmailForValidAddress(): void
    {
        // Arrange
        $address = 'user@example.com';
        $expected = 'user@example.com';

        // Act
        $result = Email::fromString($address)->asString();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function fromStringThrowsForInvalidEmailAddress(): void
    {
        // Arrange
        $invalid = 'not-an-email';

        // Assert
        $this->expectException(\InvalidArgumentException::class);

        // Act
        Email::fromString($invalid);
    }

    #[Test]
    public function fromStringThrowsForEmptyString(): void
    {
        // Assert
        $this->expectException(\InvalidArgumentException::class);

        // Act
        Email::fromString('');
    }
}
