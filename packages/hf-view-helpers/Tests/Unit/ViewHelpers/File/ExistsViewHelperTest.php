<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\ViewHelpers\File;

use Hausformat\ViewHelpers\ViewHelpers\File\ExistsViewHelper;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ExistsViewHelperTest extends UnitTestCase
{
    #[Test]
    public function verdictReturnsTrueForExistingDirectory(): void
    {
        // Arrange
        $directory = sys_get_temp_dir();

        // Act
        $result = ExistsViewHelper::verdict(['directory' => $directory]);

        // Assert
        self::assertTrue($result);
    }

    #[Test]
    public function verdictReturnsFalseForNonExistingDirectory(): void
    {
        // Arrange
        $directory = sys_get_temp_dir() . '/definitely_nonexistent_' . uniqid();

        // Act
        $result = ExistsViewHelper::verdict(['directory' => $directory]);

        // Assert
        self::assertFalse($result);
    }

    #[Test]
    public function verdictReturnsFalseWhenNoArgumentGiven(): void
    {
        // Act
        $result = ExistsViewHelper::verdict([]);

        // Assert
        self::assertFalse($result);
    }

    #[Test]
    public function verdictReturnsTrueForExistingFile(): void
    {
        // Arrange
        $tmpFile = tempnam(sys_get_temp_dir(), 'hf_test_');

        // Act
        $result = ExistsViewHelper::verdict(['file' => $tmpFile]);

        // Assert
        self::assertTrue($result);

        // Cleanup
        unlink($tmpFile);
    }
}
