<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\Tests\Unit\ViewHelpers\Widget;

use Hausformat\Lib\Pagination\AlphabeticalPaginator;
use Hausformat\ViewHelpers\ViewHelpers\Widget\PaginateAlphabeticalViewHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class PaginateAlphabeticalViewHelperTest extends UnitTestCase
{
    private \ReflectionMethod $buildPagination;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildPagination = new \ReflectionMethod(PaginateAlphabeticalViewHelper::class, 'buildPagination');
    }

    #[Test]
    public function buildPaginationReturnsPagesArrayWithOneEntryPerLetter(): void
    {
        // Arrange
        $paginator = $this->createStub(AlphabeticalPaginator::class);
        $paginator->method('getPaginationValues')->willReturn(['A' => 5, 'B' => 3, 'C' => 0]);
        $paginator->method('getResultsPerPage')->willReturn(['A' => 5, 'B' => 3, 'C' => 0]);

        $arguments = [
            'initializePage' => 1,
            'addAll' => false,
            'dontLinkEmpty' => '',
        ];

        // Act
        $result = $this->buildPagination->invoke(null, $arguments, $paginator);

        // Assert
        self::assertArrayHasKey('pages', $result);
        self::assertCount(3, $result['pages']);
    }

    #[Test]
    public function buildPaginationSetsCurrentPageCorrectly(): void
    {
        // Arrange
        $paginator = $this->createStub(AlphabeticalPaginator::class);
        $paginator->method('getPaginationValues')->willReturn(['A' => 2, 'B' => 4]);
        $paginator->method('getResultsPerPage')->willReturn(['A' => 2, 'B' => 4]);

        $arguments = [
            'initializePage' => 2,
            'addAll' => false,
            'dontLinkEmpty' => '',
        ];

        // Act
        $result = $this->buildPagination->invoke(null, $arguments, $paginator);

        // Assert
        self::assertSame(2, $result['current']);
        self::assertTrue($result['pages'][1]['isCurrent']);
        self::assertFalse($result['pages'][0]['isCurrent']);
    }

    #[Test]
    public function buildPaginationReturnsCorrectNumberOfPages(): void
    {
        // Arrange
        $paginator = $this->createStub(AlphabeticalPaginator::class);
        $paginator->method('getPaginationValues')->willReturn(['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4]);
        $paginator->method('getResultsPerPage')->willReturn(['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4]);

        $arguments = [
            'initializePage' => 1,
            'addAll' => false,
            'dontLinkEmpty' => '',
        ];

        // Act
        $result = $this->buildPagination->invoke(null, $arguments, $paginator);

        // Assert
        self::assertSame(4, $result['numberOfPages']);
    }
}
