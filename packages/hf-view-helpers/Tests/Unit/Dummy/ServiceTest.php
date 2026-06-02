<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\Dummy;

use Hausformat\ViewHelpers\Dummy\ErrorHandler;
use Hausformat\ViewHelpers\Dummy\Service;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ServiceTest extends UnitTestCase
{
    private ErrorHandler&MockObject $mockErrorHandler;

    private Service $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockErrorHandler = $this->createMock(ErrorHandler::class);
        $this->subject = new Service($this->mockErrorHandler);
    }

    #[Test]
    public function doSomethingCallsErrorHandlerWithCaughtException(): void
    {
        // Arrange
        $this->mockErrorHandler
            ->expects(self::once())
            ->method('handle')
            ->with(self::isInstanceOf(\Exception::class));

        // Act
        $this->subject->doSomething();
    }

    #[Test]
    public function doSomethingOutputsProgressMessage(): void
    {
        // Arrange
        $this->mockErrorHandler->method('handle');

        // Act + Assert
        $this->expectOutputRegex('/Do something/');
        $this->subject->doSomething();
    }

    #[Test]
    public function runDeprecationFuncReturnsTrue(): void
    {
        // Act
        $result = $this->subject->runDeprecateionFunc();

        // Assert
        self::assertTrue($result);
    }
}
