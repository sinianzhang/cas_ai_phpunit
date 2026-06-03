<?php

declare(strict_types=1);

namespace Hausformat\Lib\Tests\Unit\Utility\Resource;

use Hausformat\Lib\Domain\Type\Orientation;
use Hausformat\Lib\Utility\Resource\OrientationUtility;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class OrientationUtilityTest extends UnitTestCase
{
    #[Test]
    public function getOrientationReturnsLandscapeWhenWidthExceedsHeightAndNoCropVariant(): void
    {
        // Arrange
        $file = $this->createStub(FileInterface::class);
        $file->method('hasProperty')->willReturn(false);
        $file->method('getProperty')
            ->willReturnMap([['width', 1200], ['height', 800]]);

        // Act
        $result = OrientationUtility::getOrientation($file);

        // Assert
        $this->assertSame(Orientation::LANDSCAPE, $result);
    }

    #[Test]
    public function getOrientationReturnsPortraitWhenHeightExceedsWidthAndNoCropVariant(): void
    {
        // Arrange
        $file = $this->createStub(FileInterface::class);
        $file->method('hasProperty')->willReturn(false);
        $file->method('getProperty')
            ->willReturnMap([['width', 800], ['height', 1200]]);

        // Act
        $result = OrientationUtility::getOrientation($file);

        // Assert
        $this->assertSame(Orientation::PORTRAIT, $result);
    }

    #[Test]
    public function getOrientationReturnsSquareWhenDimensionsAreEqualAndNoCropVariant(): void
    {
        // Arrange
        $file = $this->createStub(FileInterface::class);
        $file->method('hasProperty')->willReturn(false);
        $file->method('getProperty')
            ->willReturnMap([['width', 1000], ['height', 1000]]);

        // Act
        $result = OrientationUtility::getOrientation($file);

        // Assert
        $this->assertSame(Orientation::SQUARE, $result);
    }
}
