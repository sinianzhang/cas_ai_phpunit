<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\Dummy;

use Hausformat\ViewHelpers\Dummy\Greeter;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class GreeterTest extends UnitTestCase
{
    private Greeter $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new Greeter();
    }

    #[Test]
    public function greetReturnsHelloWithName(): void
    {
        // Arrange
        $expected = 'Hello, World!';

        // Act
        $result = $this->subject->greet('World');

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function greetWithTimeOfDayReturnsMorningGreetingBeforeNoon(): void
    {
        // Arrange
        $expected = 'Good morning, Alice!';

        // Act
        $result = $this->subject->greetWithTimeOfDay('Alice', 8);

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function greetWithTimeOfDayReturnsAfternoonGreetingAtNoon(): void
    {
        // Arrange
        $expected = 'Good afternoon, Alice!';

        // Act
        $result = $this->subject->greetWithTimeOfDay('Alice', 12);

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function greetWithTimeOfDayReturnsEveningGreetingAtEighteen(): void
    {
        // Arrange
        $expected = 'Good evening, Alice!';

        // Act
        $result = $this->subject->greetWithTimeOfDay('Alice', 18);

        // Assert
        self::assertSame($expected, $result);
    }
}
