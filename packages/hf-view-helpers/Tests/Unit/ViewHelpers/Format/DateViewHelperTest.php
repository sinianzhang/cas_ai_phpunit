<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\ViewHelpers\Format;

use Hausformat\ViewHelpers\ViewHelpers\Format\DateViewHelper;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class DateViewHelperTest extends UnitTestCase
{
    private DateViewHelper $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new DateViewHelper();
        $this->subject->initializeArguments();
    }

    #[Test]
    public function renderFormatsDateTimeObjectWithGivenFormat(): void
    {
        // Arrange
        $date = new \DateTime('2024-06-15');
        $expected = '15.06.2024';

        // Act
        $this->subject->setArguments(['date' => $date, 'format' => 'd.m.Y', 'strftime' => false]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderParsesDateStringAndFormats(): void
    {
        // Arrange
        $expected = '2024';

        // Act
        $this->subject->setArguments(['date' => '2024-01-01', 'format' => 'Y', 'strftime' => false]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderReturnsEmptyStringWhenDateAndChildrenAreNull(): void
    {
        // Arrange
        $expected = '';
        $this->subject->setRenderChildrenClosure(fn() => null);

        // Act
        $this->subject->setArguments(['date' => null, 'format' => 'Y', 'strftime' => false]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }
}
