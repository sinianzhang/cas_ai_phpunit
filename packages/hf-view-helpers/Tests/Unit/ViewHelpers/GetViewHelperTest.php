<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\ViewHelpers;

use Hausformat\ViewHelpers\ViewHelpers\GetViewHelper;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class GetViewHelperTest extends UnitTestCase
{
    private GetViewHelper $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new GetViewHelper();
        $this->subject->initializeArguments();
    }

    #[Test]
    public function renderReturnsValueFromFlatArray(): void
    {
        // Arrange
        $expected = 'hello';

        // Act
        $this->subject->setArguments(['obj' => ['greeting' => 'hello'], 'property' => 'greeting', 'multipleAsArray' => false]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderReturnsNullForMissingProperty(): void
    {
        // Act
        $this->subject->setArguments(['obj' => ['greeting' => 'hello'], 'property' => 'nonexistent', 'multipleAsArray' => false]);
        $result = $this->subject->render();

        // Assert
        self::assertNull($result);
    }

    #[Test]
    public function renderReturnsValueFromNestedArray(): void
    {
        // Arrange
        $expected = 'deep value';
        $data = ['level1' => ['level2' => 'deep value']];

        // Act
        $this->subject->setArguments(['obj' => $data, 'property' => 'level1.level2', 'multipleAsArray' => false]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }
}
