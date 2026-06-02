<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\ViewHelpers\Format;

use Hausformat\ViewHelpers\ViewHelpers\Format\CleanHtmlViewHelper;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class CleanHtmlViewHelperTest extends UnitTestCase
{
    private CleanHtmlViewHelper $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new CleanHtmlViewHelper();
        $this->subject->initializeArguments();
    }

    #[Test]
    public function renderRemovesParagraphsContainingOnlyNbsp(): void
    {
        // Arrange
        $input = '<p>real content</p><p>&nbsp;</p><p>more content</p>';
        $expected = '<p>real content</p><p>more content</p>';

        // Act
        $this->subject->setArguments(['value' => $input]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderKeepsParagraphsWithActualContent(): void
    {
        // Arrange
        $input = '<p>Hello World</p>';
        $expected = '<p>Hello World</p>';

        // Act
        $this->subject->setArguments(['value' => $input]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderReturnsEmptyStringForEmptyInput(): void
    {
        // Arrange
        $expected = '';

        // Act
        $this->subject->setArguments(['value' => '']);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }
}
