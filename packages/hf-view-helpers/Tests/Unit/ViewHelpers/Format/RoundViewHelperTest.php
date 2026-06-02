<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\ViewHelpers\Format;

use Hausformat\ViewHelpers\ViewHelpers\Format\RoundViewHelper;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class RoundViewHelperTest extends UnitTestCase
{
    private RoundViewHelper $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new RoundViewHelper();
        $this->subject->initializeArguments();
    }

    #[Test]
    public function renderRoundsUpToHalfByDefault(): void
    {
        // Arrange — 12.3 → ceil(12.3 * 2) / 2 = ceil(24.6) / 2 = 25 / 2 = 12.5
        $expected = '12.5';

        // Act
        $this->subject->setArguments([
            'value' => '12.3',
            'divider' => 0,
            'numberFormat' => '',
            'removeTextParts' => true,
            'roundType' => 'up-to-half',
            'decimals' => 0,
            'thousandsSeparator' => '',
            'decimalPoint' => '',
        ]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderRoundsCeilForRoundModeUp(): void
    {
        // Arrange
        $expected = '13';

        // Act
        $this->subject->setArguments([
            'value' => '12.3',
            'divider' => 0,
            'numberFormat' => '',
            'removeTextParts' => true,
            'roundType' => 'up',
            'decimals' => 0,
            'thousandsSeparator' => '',
            'decimalPoint' => '',
        ]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderRoundsWithDecimalsWhenSpecified(): void
    {
        // Arrange — round(12.3456, 2) = 12.35
        $expected = '12.35';

        // Act
        $this->subject->setArguments([
            'value' => '12.3456',
            'divider' => 0,
            'numberFormat' => '',
            'removeTextParts' => false,
            'roundType' => 'round',
            'decimals' => 2,
            'thousandsSeparator' => '',
            'decimalPoint' => '',
        ]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }
}
