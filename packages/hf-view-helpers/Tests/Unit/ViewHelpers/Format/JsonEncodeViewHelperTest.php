<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\ViewHelpers\Format;

use Hausformat\ViewHelpers\ViewHelpers\Format\JsonEncodeViewHelper;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class JsonEncodeViewHelperTest extends UnitTestCase
{
    private JsonEncodeViewHelper $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new JsonEncodeViewHelper();
        $this->subject->initializeArguments();
    }

    #[Test]
    public function renderEncodesArrayToJsonString(): void
    {
        // Arrange
        $expected = '["a","b","c"]';

        // Act
        $this->subject->setArguments(['value' => ['a', 'b', 'c'], 'options' => 0, 'pretty' => false, 'forceObject' => false]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderEncodesEmptyArrayToEmptyJsonArray(): void
    {
        // Arrange
        $expected = '[]';

        // Act
        $this->subject->setArguments(['value' => [], 'options' => 0, 'pretty' => false, 'forceObject' => false]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderEncodesWithForceObjectOption(): void
    {
        // Arrange
        $expected = '{"0":"a","1":"b"}';

        // Act
        $this->subject->setArguments(['value' => ['a', 'b'], 'options' => 0, 'pretty' => false, 'forceObject' => true]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }
}
