<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\ViewHelpers;

use Hausformat\ViewHelpers\ViewHelpers\SortObjectStorageViewHelper;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class SortObjectStorageViewHelperTest extends UnitTestCase
{
    private SortObjectStorageViewHelper $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new SortObjectStorageViewHelper();
        $this->subject->initializeArguments();
    }

    #[Test]
    public function renderReturnsEmptyArrayForEmptyStorage(): void
    {
        // Arrange
        $expected = 0;

        // Act
        $this->subject->setArguments(['storage' => [], 'orderByField' => 'uid', 'mode' => 'default', 'useStrCompare' => false]);
        $result = $this->subject->render();

        // Assert
        self::assertCount($expected, $result);
    }

    #[Test]
    public function renderReturnsArrayForArrayInput(): void
    {
        // Arrange
        $storage = [
            ['uid' => 3, 'title' => 'C'],
            ['uid' => 1, 'title' => 'A'],
            ['uid' => 2, 'title' => 'B'],
        ];

        // Act
        $this->subject->setArguments(['storage' => $storage, 'orderByField' => 'uid', 'mode' => 'default', 'useStrCompare' => false]);
        $result = $this->subject->render();

        // Assert
        self::assertCount(3, $result);
    }

    #[Test]
    public function renderReturnsShuffledResultForShuffleMode(): void
    {
        // Arrange
        $storage = [
            ['uid' => 1],
            ['uid' => 2],
            ['uid' => 3],
        ];

        // Act
        $this->subject->setArguments(['storage' => $storage, 'orderByField' => 'uid', 'mode' => 'shuffle', 'useStrCompare' => false]);
        $result = $this->subject->render();

        // Assert
        self::assertCount(3, $result);
    }
}
