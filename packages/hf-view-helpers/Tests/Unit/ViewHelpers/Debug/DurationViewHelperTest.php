<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\ViewHelpers\Debug;

use Hausformat\ViewHelpers\ViewHelpers\Debug\DurationViewHelper;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class DurationViewHelperTest extends UnitTestCase
{
    private DurationViewHelper $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new DurationViewHelper();
        $this->subject->initializeArguments();
        $this->subject->setRenderChildrenClosure(fn(): string => '');
    }

    #[Test]
    public function renderReturnsString(): void
    {
        // Arrange
        $this->subject->setArguments(['name' => null, 'title' => null, 'plainText' => true, 'ansiColors' => false, 'inline' => false]);

        // Act
        $result = $this->subject->render();

        // Assert
        self::assertIsString($result);
    }

    #[Test]
    public function renderUsesNameAsTitleWhenTitleIsEmpty(): void
    {
        // Arrange
        $expectedLabel = 'myTimer';
        $this->subject->setArguments(['name' => 'myTimer', 'title' => null, 'plainText' => true, 'ansiColors' => false, 'inline' => false]);

        // Act
        $result = $this->subject->render();

        // Assert
        self::assertStringContainsString($expectedLabel, $result);
    }

    #[Test]
    public function renderPrefersTitleOverName(): void
    {
        // Arrange
        $expectedLabel = 'explicitTitle';
        $this->subject->setArguments(['name' => 'myTimer', 'title' => 'explicitTitle', 'plainText' => true, 'ansiColors' => false, 'inline' => false]);

        // Act
        $result = $this->subject->render();

        // Assert
        self::assertStringContainsString($expectedLabel, $result);
    }
}
