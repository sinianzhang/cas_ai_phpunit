<?php

declare(strict_types=1);

namespace Hausformat\Lib\Tests\Unit\Domain\Type;

use Hausformat\Lib\Domain\Type\Orientation;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class OrientationTest extends UnitTestCase
{
    #[Test]
    public function isLandscapeReturnsTrueForLandscapeCase(): void
    {
        $this->assertTrue(Orientation::LANDSCAPE->isLandscape());
    }

    #[Test]
    public function isLandscapeReturnsFalseForPortraitCase(): void
    {
        $this->assertFalse(Orientation::PORTRAIT->isLandscape());
    }

    #[Test]
    public function isLandscapeReturnsFalseForSquareCase(): void
    {
        $this->assertFalse(Orientation::SQUARE->isLandscape());
    }

    #[Test]
    public function isPortraitReturnsTrueForPortraitCase(): void
    {
        $this->assertTrue(Orientation::PORTRAIT->isPortrait());
    }

    #[Test]
    public function isPortraitReturnsFalseForLandscapeCase(): void
    {
        $this->assertFalse(Orientation::LANDSCAPE->isPortrait());
    }

    #[Test]
    public function isSquareReturnsTrueForSquareCase(): void
    {
        $this->assertTrue(Orientation::SQUARE->isSquare());
    }

    #[Test]
    public function isSquareReturnsFalseForLandscapeCase(): void
    {
        $this->assertFalse(Orientation::LANDSCAPE->isSquare());
    }

    #[Test]
    public function enumCasesHaveCorrectStringValues(): void
    {
        $this->assertSame('landscape', Orientation::LANDSCAPE->value);
        $this->assertSame('portrait', Orientation::PORTRAIT->value);
        $this->assertSame('square', Orientation::SQUARE->value);
    }
}
