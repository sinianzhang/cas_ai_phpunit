<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\ViewHelpers;

use Hausformat\ViewHelpers\ViewHelpers\CalcViewHelper;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class CalcViewHelperTest extends UnitTestCase
{
    private CalcViewHelper $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new CalcViewHelper();
        $this->subject->initializeArguments();
    }

    #[Test]
    public function renderRoundsHalfByDefault(): void
    {
        // Arrange
        $expected = 4.0;

        // Act
        $this->subject->setArguments(['value' => '2+2', 'round' => 'half']);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderReturnsPreciseFloatForDivision(): void
    {
        // Arrange
        $expected = 2.5;

        // Act
        $this->subject->setArguments(['value' => '5/2', 'round' => 'precise']);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderRoundsCeilForRoundModeUp(): void
    {
        // Arrange
        $expected = 3.0;

        // Act
        $this->subject->setArguments(['value' => '5/2', 'round' => 'up']);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderThrowsForExpressionWithCurlyBraces(): void
    {
        // Arrange
        $this->subject->setArguments(['value' => '{unresolved}', 'round' => 'half']);

        // Assert
        $this->expectException(\InvalidArgumentException::class);

        // Act
        $this->subject->render();
    }
}
