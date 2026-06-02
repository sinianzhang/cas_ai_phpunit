<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\ViewHelpers\Format;

use Hausformat\ViewHelpers\ViewHelpers\Format\WhitespaceViewHelper;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class WhitespaceViewHelperTest extends UnitTestCase
{
    private WhitespaceViewHelper $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new WhitespaceViewHelper();
        $this->subject->initializeArguments();
    }

    #[Test]
    public function renderCollapsesMultipleWhitespacesAndTrims(): void
    {
        // Arrange
        $expected = 'hello world !';

        // Act
        $this->subject->setArguments(['value' => '  hello   world  !  ', 'trim' => true]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderCollapsesWhitespacesWithoutTrimmingWhenTrimIsFalse(): void
    {
        // Arrange — leading and trailing single space preserved, internal collapses
        $expected = ' hello world ';

        // Act
        $this->subject->setArguments(['value' => '  hello   world  ', 'trim' => false]);
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
        $this->subject->setArguments(['value' => '', 'trim' => true]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }
}
