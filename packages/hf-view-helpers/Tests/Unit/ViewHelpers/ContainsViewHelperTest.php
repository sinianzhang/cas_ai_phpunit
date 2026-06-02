<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\ViewHelpers;

use Hausformat\ViewHelpers\ViewHelpers\ContainsViewHelper;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ContainsViewHelperTest extends UnitTestCase
{
    private ContainsViewHelper $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new ContainsViewHelper();
        $this->subject->initializeArguments();
    }

    #[Test]
    public function renderReturnsTrueWhenStringContainsNeedle(): void
    {
        // Arrange
        $expected = true;

        // Act
        $this->subject->setArguments(['haystack' => 'hello world', 'needle' => 'world', 'returnAmount' => false, 'path' => null]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderReturnsFalseWhenStringDoesNotContainNeedle(): void
    {
        // Arrange
        $expected = false;

        // Act
        $this->subject->setArguments(['haystack' => 'hello world', 'needle' => 'xyz', 'returnAmount' => false, 'path' => null]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderReturnsOccurrenceCountWhenReturnAmountIsTrue(): void
    {
        // Arrange
        $expected = 3;

        // Act
        $this->subject->setArguments(['haystack' => 'aaa', 'needle' => 'a', 'returnAmount' => true, 'path' => null]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderReturnsTrueWhenArrayContainsNeedle(): void
    {
        // Arrange
        $expected = true;

        // Act
        $this->subject->setArguments(['haystack' => ['apple', 'banana', 'cherry'], 'needle' => 'banana', 'returnAmount' => false, 'path' => null]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }
}
