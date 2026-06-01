<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\Dummy;

use Hausformat\ViewHelpers\Dummy\ErrorHandler;
use Hausformat\ViewHelpers\Dummy\ErrorHandlerInterface;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ErrorHandlerTest extends UnitTestCase
{
    #[Test]
    public function implementsErrorHandlerInterface(): void
    {
        self::assertInstanceOf(ErrorHandlerInterface::class, new ErrorHandler());
    }

    #[Test]
    #[RunInSeparateProcess]
    public function handleOutputsExceptionMessageWithTrailingNewline(): void
    {
        // Arrange
        $exception = new \Exception('Something went wrong');
        $subject = new ErrorHandler();

        // Act + Assert
        $this->expectOutputString("Something went wrong\n");
        $subject->handle($exception);
    }

    #[Test]
    #[RunInSeparateProcess]
    public function handleOutputsEmptyLineForExceptionWithEmptyMessage(): void
    {
        // Arrange
        $exception = new \Exception('');
        $subject = new ErrorHandler();

        // Act + Assert
        $this->expectOutputString("\n");
        $subject->handle($exception);
    }

    #[Test]
    #[RunInSeparateProcess]
    public function handleOutputsMessageFromRuntimeException(): void
    {
        // Arrange
        $exception = new \RuntimeException('Runtime error occurred');
        $subject = new ErrorHandler();

        // Act + Assert
        $this->expectOutputString("Runtime error occurred\n");
        $subject->handle($exception);
    }
}
