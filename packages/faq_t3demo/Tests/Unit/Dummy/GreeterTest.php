<?php declare(strict_types=1);

namespace B13\FaqT3demo\Tests\Unit\Dummy;

use B13\FaqT3demo\Dummy\Greeter;
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
        $expected = 'Hello, Alice!';

        // Act
        $result = $this->subject->greet('Alice');

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function greetReturnsHelloWithEmptyName(): void
    {
        // Arrange
        $expected = 'Hello, !';

        // Act
        $result = $this->subject->greet('');

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function greetWithTimeOfDayReturnsMorningGreetingForHourBeforeNoon(): void
    {
        // Arrange
        $expected = 'Good morning, Bob!';

        // Act
        $result = $this->subject->greetWithTimeOfDay('Bob', 9);

        // Assert
        self::assertSame($expected, $result);
    }

    // TODO: add a test for greeting afternoom

    // TODO: add a test fro greeting everning
}
