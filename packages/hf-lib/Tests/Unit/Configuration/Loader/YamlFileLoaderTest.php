<?php

declare(strict_types=1);

namespace Hausformat\Lib\Tests\Unit\Configuration\Loader;

use Hausformat\Lib\Configuration\Loader\YamlFileLoader;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class YamlFileLoaderTest extends UnitTestCase
{
    private \TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader&Stub $innerLoaderStub;
    private YamlFileLoader $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->innerLoaderStub = $this->createStub(\TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader::class);
        GeneralUtility::addInstance(\TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader::class, $this->innerLoaderStub);
        $this->subject = new YamlFileLoader();
    }

    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();
        parent::tearDown();
    }

    #[Test]
    public function loadReturnsUnmodifiedArrayWhenNoPlaceholdersPresent(): void
    {
        // Arrange
        $expected = ['key' => 'value', 'another' => 'data'];
        $this->innerLoaderStub->method('load')->willReturn($expected);

        // Act
        $result = $this->subject->load('dummy.yaml');

        // Assert
        $this->assertSame($expected, $result);
    }

    #[Test]
    public function loadResolvesSimplePlaceholderFromReferenceKey(): void
    {
        // Arrange
        $this->innerLoaderStub->method('load')->willReturn([
            'greeting' => 'Hello',
            'message' => '$greeting$',
        ]);

        // Act
        $result = $this->subject->load('dummy.yaml');

        // Assert
        $this->assertSame(['greeting' => 'Hello', 'message' => 'Hello'], $result);
    }

    #[Test]
    public function loadResolvesPlaceholderInsideNestedArray(): void
    {
        // Arrange
        $this->innerLoaderStub->method('load')->willReturn([
            'base' => 'world',
            'nested' => ['msg' => '$base$'],
        ]);

        // Act
        $result = $this->subject->load('dummy.yaml');

        // Assert
        $this->assertSame(['base' => 'world', 'nested' => ['msg' => 'world']], $result);
    }

    #[Test]
    public function loadReturnsUnsubstitutedPlaceholderWhenKeyNotFound(): void
    {
        // Arrange
        $this->innerLoaderStub->method('load')->willReturn(['key' => '$missing$']);

        // Act
        $result = $this->subject->load('dummy.yaml');

        // Assert
        $this->assertSame(['key' => '$missing$'], $result);
    }
}
