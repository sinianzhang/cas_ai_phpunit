<?php declare(strict_types=1);

namespace B13\FaqT3demo\Tests\Unit\Hooks;

use B13\FaqT3demo\Hooks\DataHandlerCacheHook;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class DataHandlerCacheHookTest extends UnitTestCase
{
    private FrontendInterface&MockObject $cachePages;
    private DataHandlerCacheHook $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cachePages = $this->createMock(FrontendInterface::class);
        $this->subject = new DataHandlerCacheHook($this->cachePages);
    }

    #[Test]
    public function flushesCacheTagWhenFaqTableIsAffected(): void
    {
        // Arrange
        $dataHandler = $this->createStub(DataHandler::class);
        $this->cachePages->expects(self::once())
            ->method('flushByTag')
            ->with('tx-faq_t3demo');

        // Act
        $this->subject->processDatamap_afterDatabaseOperations('new', 'tx_faqt3demo_faq', 42, [], $dataHandler);
    }

    #[Test]
    public function doesNotFlushCacheTagForUnrelatedTable(): void
    {
        // Arrange
        $dataHandler = $this->createStub(DataHandler::class);
        $this->cachePages->expects(self::never())
            ->method('flushByTag');

        // Act
        $this->subject->processDatamap_afterDatabaseOperations('update', 'pages', 1, [], $dataHandler);
    }

    #[Test]
    public function flushesCacheTagRegardlessOfOperationStatus(): void
    {
        // Arrange
        $dataHandler = $this->createStub(DataHandler::class);
        $this->cachePages->expects(self::once())
            ->method('flushByTag')
            ->with('tx-faq_t3demo');

        // Act
        $this->subject->processDatamap_afterDatabaseOperations('update', 'tx_faqt3demo_faq', 99, ['field' => 'value'], $dataHandler);
    }
}
