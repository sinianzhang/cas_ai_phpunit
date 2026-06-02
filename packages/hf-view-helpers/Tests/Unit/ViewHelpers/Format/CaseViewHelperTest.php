<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\ViewHelpers\Format;

use Hausformat\ViewHelpers\ViewHelpers\Format\CaseViewHelper;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class CaseViewHelperTest extends UnitTestCase
{
    private CaseViewHelper $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new CaseViewHelper();
        $this->subject->initializeArguments();
    }

    #[Test]
    public function renderConvertsStringToLowerCase(): void
    {
        // Arrange
        $expected = 'hello world';

        // Act
        $this->subject->setArguments(['value' => 'Hello World', 'case' => 'lower']);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderConvertsStringToUpperCase(): void
    {
        // Arrange
        $expected = 'HELLO WORLD';

        // Act
        $this->subject->setArguments(['value' => 'Hello World', 'case' => 'upper']);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderReturnsEmptyStringForNullValue(): void
    {
        // Arrange
        $expected = '';
        $this->subject->setRenderChildrenClosure(fn() => null);

        // Act
        $this->subject->setArguments(['value' => '', 'case' => 'lower']);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }
}
