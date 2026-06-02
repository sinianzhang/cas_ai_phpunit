<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\ViewHelpers;

use Hausformat\ViewHelpers\ViewHelpers\ForViewHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\Variables\VariableProviderInterface;

final class ForViewHelperTest extends UnitTestCase
{
    private RenderingContextInterface&Stub $renderingContext;
    private VariableProviderInterface&Stub $variableProvider;
    private ForViewHelper $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->variableProvider = $this->createStub(VariableProviderInterface::class);
        $this->renderingContext = $this->createStub(RenderingContextInterface::class);
        $this->renderingContext->method('getVariableProvider')->willReturn($this->variableProvider);

        $this->subject = new ForViewHelper();
        $this->subject->setRenderingContext($this->renderingContext);
        $this->subject->initializeArguments();
    }

    #[Test]
    public function renderExecutesLoopExactNumberOfTimes(): void
    {
        // Arrange
        $callCount = 0;
        $this->subject->setRenderChildrenClosure(function () use (&$callCount): string {
            $callCount++;
            return 'x';
        });

        // Act
        $this->subject->setArguments(['limit' => 3, 'begin' => 1, 'increment' => 1, 'variable' => 'i', 'counter' => 'c']);
        $this->subject->render();

        // Assert
        self::assertSame(3, $callCount);
    }

    #[Test]
    public function renderAccumulatesChildrenOutput(): void
    {
        // Arrange
        $expected = 'aaa';
        $this->subject->setRenderChildrenClosure(fn(): string => 'a');

        // Act
        $this->subject->setArguments(['limit' => 3, 'begin' => 1, 'increment' => 1, 'variable' => 'i', 'counter' => 'c']);
        $result = $this->subject->render();

        // Assert
        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderRespectsIncrementArgument(): void
    {
        // Arrange — begin=0, limit=4, increment=2 → iterations at 0,2,4 → 3 calls
        $callCount = 0;
        $this->subject->setRenderChildrenClosure(function () use (&$callCount): string {
            $callCount++;
            return '';
        });

        // Act
        $this->subject->setArguments(['limit' => 4, 'begin' => 0, 'increment' => 2, 'variable' => 'i', 'counter' => 'c']);
        $this->subject->render();

        // Assert
        self::assertSame(3, $callCount);
    }
}
