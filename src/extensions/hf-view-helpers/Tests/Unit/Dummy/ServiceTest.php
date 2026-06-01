<?php declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\Dummy;

use Hausformat\ViewHelpers\Dummy\ErrorHandler;
use Hausformat\ViewHelpers\Dummy\Service;
use Hausformat\ViewHelpers\Dummy\ServiceInterface;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\MockObject\NeverReturningMethodException;
use PHPUnit\Framework\TestCase;

final class ServiceTest extends TestCase
{
    public function testImplementsServiceInterface(): void
    {
        $this->assertInstanceOf(ServiceInterface::class, new Service(new ErrorHandler()));
    }

    public function testDoSomethingPassesExceptionToHandler(): void
    {
        $errorHandler = $this->createMock(ErrorHandler::class);
        $errorHandler
            ->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(\Exception::class))
            ->seal();

        // ErrorHandler::handle() is declared to never return     
        $this->expectException(NeverReturningMethodException::class);
        $service = new Service($errorHandler);
        $service->doSomething();
    }

    public function testRunDeprecationFunc(): void
    {
        $service = new Service(new ErrorHandler());
        $this->assertTrue($service->runDeprecateionFunc());
    }
}
