<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\ViewHelpers\Format;

use Hausformat\ViewHelpers\ViewHelpers\Format\JsonDecodeViewHelper;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class JsonDecodeViewHelperTest extends UnitTestCase
{
    private JsonDecodeViewHelper $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new JsonDecodeViewHelper();
        $this->subject->initializeArguments();
    }

    #[Test]
    public function renderDecodesJsonObjectToAssociativeArray(): void
    {
        // Arrange
        $expected = ['name' => 'Alice', 'age' => 30];

        // Act
        $this->subject->setArguments(['json' => '{"name":"Alice","age":30}', 'assoc' => true, 'depth' => 512]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderReturnsNullForEmptyJsonString(): void
    {
        // Act
        $this->subject->setArguments(['json' => '', 'assoc' => false, 'depth' => 512]);
        $result = $this->subject->render();

        // Assert
        self::assertNull($result);
    }

    #[Test]
    public function renderThrowsExceptionForInvalidJson(): void
    {
        // Arrange
        $this->subject->setArguments(['json' => 'not valid json {]', 'assoc' => false, 'depth' => 512]);

        // Assert
        $this->expectException(\Exception::class);

        // Act
        $this->subject->render();
    }
}
