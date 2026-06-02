<?php

declare(strict_types=1);

namespace Hausformat\Lib\Tests\Unit\Context;

use Hausformat\Lib\Context\Context;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Context\UserAspect;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ContextTest extends UnitTestCase
{
    private \TYPO3\CMS\Core\Context\Context&MockObject $mockContext;
    private Context $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockContext = $this->createMock(\TYPO3\CMS\Core\Context\Context::class);
        $this->subject = new Context($this->mockContext);
    }

    #[Test]
    public function getLanguageAspectReturnsLanguageAspect(): void
    {
        // Arrange
        $languageAspect = new LanguageAspect();
        $this->mockContext->expects($this->once())
            ->method('getAspect')
            ->with('language')
            ->willReturn($languageAspect);

        // Act
        $result = $this->subject->getLanguageAspect();

        // Assert
        $this->assertSame($languageAspect, $result);
    }

    #[Test]
    public function getFrontendUserAspectReturnsUserAspect(): void
    {
        // Arrange
        $userAspect = new UserAspect();
        $this->mockContext->expects($this->once())
            ->method('getAspect')
            ->with('frontend.user')
            ->willReturn($userAspect);

        // Act
        $result = $this->subject->getFrontendUserAspect();

        // Assert
        $this->assertSame($userAspect, $result);
    }

    #[Test]
    public function getBackendUserAspectReturnsUserAspect(): void
    {
        // Arrange
        $userAspect = new UserAspect();
        $this->mockContext->expects($this->once())
            ->method('getAspect')
            ->with('backend.user')
            ->willReturn($userAspect);

        // Act
        $result = $this->subject->getBackendUserAspect();

        // Assert
        $this->assertSame($userAspect, $result);
    }

    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function getLanguageAspectThrowsRuntimeExceptionWhenAspectNotFound(): void
    {
        // Arrange
        $this->mockContext->method('getAspect')
            ->willThrowException(new AspectNotFoundException());

        // Act & Assert
        $this->expectException(\RuntimeException::class);
        $this->subject->getLanguageAspect();
    }
}
