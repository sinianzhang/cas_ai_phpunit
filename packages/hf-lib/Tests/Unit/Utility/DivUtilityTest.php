<?php

declare(strict_types=1);

namespace Hausformat\Lib\Tests\Unit\Utility;

use Hausformat\Lib\Utility\DivUtility;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class DivUtilityTest extends UnitTestCase
{
    #[Test]
    public function countFileLinesReturnsFalseForDisallowedPath(): void
    {
        $result = DivUtility::countFileLines('/etc/passwd');

        $this->assertFalse($result);
    }

    #[Test]
    public function sanitizeHexColorStripsLeadingHashAndReturnsLowercase(): void
    {
        $this->assertSame('ff0000', DivUtility::sanitizeHexColor('#FF0000'));
    }

    #[Test]
    public function sanitizeHexColorExpandsThreeCharHexToSix(): void
    {
        $this->assertSame('aabbcc', DivUtility::sanitizeHexColor('ABC'));
    }

    #[Test]
    public function sanitizeHexColorReturnsFalseForInvalidLength(): void
    {
        $this->assertFalse(DivUtility::sanitizeHexColor('FFFFF'));
    }

    #[Test]
    public function sanitizeHexColorReturnsFalseForEmptyString(): void
    {
        $this->assertFalse(DivUtility::sanitizeHexColor(''));
    }

    #[Test]
    public function hexToRgbConvertsValidHexToRgbArray(): void
    {
        $result = DivUtility::hexToRgb('#FF0000');

        $this->assertSame([255, 0, 0], $result);
    }

    #[Test]
    public function hexToRgbConvertsBlackHexToZeroArray(): void
    {
        $result = DivUtility::hexToRgb('#000000');

        $this->assertSame([0, 0, 0], $result);
    }

    #[Test]
    public function hexToRgbReturnsNullForInvalidHex(): void
    {
        $this->assertNull(DivUtility::hexToRgb('INVALID'));
    }

    #[Test]
    public function rgbToHexConvertsRgbComponentsToHexString(): void
    {
        $this->assertSame('ff0000', DivUtility::rgbToHex(255, 0, 0));
    }

    #[Test]
    public function rgbToHexPadsLowValuesWithLeadingZero(): void
    {
        $this->assertSame('000000', DivUtility::rgbToHex(0, 0, 0));
    }

    #[Test]
    public function rgbToHexProducesCorrectMixedColor(): void
    {
        $this->assertSame('1a2b3c', DivUtility::rgbToHex(26, 43, 60));
    }

    #[Test]
    public function inArrayMultiReturnsTrueWhenAnyNeedleFoundInHaystack(): void
    {
        $this->assertTrue(DivUtility::inArrayMulti(['b', 'x'], ['a', 'b', 'c']));
    }

    #[Test]
    public function inArrayMultiReturnsFalseWhenNoNeedleFoundInHaystack(): void
    {
        $this->assertFalse(DivUtility::inArrayMulti(['x', 'y'], ['a', 'b', 'c']));
    }

    #[Test]
    public function inArrayMultiReturnsFalseForEmptyNeedle(): void
    {
        $this->assertFalse(DivUtility::inArrayMulti([], ['a', 'b', 'c']));
    }
}
