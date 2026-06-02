<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\ViewHelpers\String;

use Hausformat\ViewHelpers\ViewHelpers\String\ExplodeViewHelper;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ExplodeViewHelperTest extends UnitTestCase
{
    private ExplodeViewHelper $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new ExplodeViewHelper();
        $this->subject->initializeArguments();
    }

    #[Test]
    public function renderExplodesAndTrimsItemsByDefault(): void
    {
        // Arrange
        $expected = ['a', 'b', 'c'];

        // Act
        $this->subject->setArguments(['string' => 'a, b, c', 'separator' => ',', 'trim' => true, 'removeEmptyValues' => true]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderKeepsWhitespaceWhenTrimIsFalse(): void
    {
        // Arrange
        $expected = ['a', ' b', ' c'];

        // Act
        $this->subject->setArguments(['string' => 'a, b, c', 'separator' => ',', 'trim' => false, 'removeEmptyValues' => false]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderRemovesEmptyValuesWhenFlagIsSet(): void
    {
        // Arrange
        $expected = ['a', 'c'];

        // Act
        $this->subject->setArguments(['string' => 'a,,c', 'separator' => ',', 'trim' => false, 'removeEmptyValues' => true]);
        $result = $this->subject->render();

        // Assert
        self::assertSame(array_values($expected), array_values($result));
    }
}
