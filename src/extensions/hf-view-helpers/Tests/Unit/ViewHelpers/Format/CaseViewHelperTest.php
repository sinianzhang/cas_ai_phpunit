<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\ViewHelpers\Format;

use Hausformat\ViewHelpers\ViewHelpers\Format\CaseViewHelper;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class CaseViewHelperTest extends UnitTestCase
{
    private CaseViewHelper $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new CaseViewHelper();
    }

    #[Test]
    public function renderReturnsCamelCase(): void
    {
        // Arrange
        $this->subject->setArguments(['value' => 'hello_world', 'case' => 'camelCase']);

        // Act
        $result = $this->subject->render();

        // Assert
        $this->assertSame('helloWorld', $result);
    }

    // TODO: add a test for upperCamelCase
    // TODO: add a test for upperCase
}
