<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\ViewHelpers\Format;

use Hausformat\ViewHelpers\ViewHelpers\Format\NumberViewHelper;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class NumberViewHelperTest extends UnitTestCase
{
    private NumberViewHelper $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new NumberViewHelper();
        $this->subject->initializeArguments();
    }

    #[Test]
    public function renderReturnsNonNumericValueAsIs(): void
    {
        // Arrange
        $expected = 'not-a-number';

        // Act
        $this->subject->setArguments([
            'value' => 'not-a-number',
            'format' => '',
            'prefix' => '',
            'suffix' => '',
            'fillInt' => '0',
            'fillFloat' => '0',
            'returnZeroAsEmptyString' => false,
            'zeroEmptyString' => '',
            'dontShowMinusSign' => false,
            'factor' => 1,
        ]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderReturnsZeroEmptyStringWhenValueIsZero(): void
    {
        // Arrange
        $expected = 'no value';

        // Act
        $this->subject->setArguments([
            'value' => '0',
            'format' => '',
            'prefix' => '',
            'suffix' => '',
            'fillInt' => '0',
            'fillFloat' => '0',
            'returnZeroAsEmptyString' => true,
            'zeroEmptyString' => 'no value',
            'dontShowMinusSign' => false,
            'factor' => 1,
        ]);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderAppendsPrefixAndSuffix(): void
    {
        // Arrange
        $expected = '$42';

        // Act
        $this->subject->setArguments([
            'value' => '42',
            'format' => '0.',
            'prefix' => '$',
            'suffix' => '',
            'fillInt' => '0',
            'fillFloat' => '0',
            'returnZeroAsEmptyString' => false,
            'zeroEmptyString' => '',
            'dontShowMinusSign' => false,
            'factor' => 1,
        ]);
        $result = $this->subject->render();

        // Assert
        self::assertStringContainsString('$', $result);
        self::assertStringContainsString('42', $result);
    }
}
