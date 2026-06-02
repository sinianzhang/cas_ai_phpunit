<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\Dummy;

use Hausformat\ViewHelpers\Dummy\ErrorHandler;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ErrorHandlerTest extends UnitTestCase
{
    private ErrorHandler $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new ErrorHandler();
    }

    #[Test]
    public function handleOutputsExceptionMessage(): void
    {
        // Arrange
        $expected = "Something went wrong\n";
        $exception = new \Exception('Something went wrong');

        // Act + Assert
        $this->expectOutputString($expected);
        $this->subject->handle($exception);
    }

    #[Test]
    public function handleOutputsMessageWithTrailingNewline(): void
    {
        // Arrange
        $message = 'Disk full';
        $exception = new \Exception($message);

        // Act + Assert
        $this->expectOutputRegex('/^Disk full\n$/');
        $this->subject->handle($exception);
    }
}
