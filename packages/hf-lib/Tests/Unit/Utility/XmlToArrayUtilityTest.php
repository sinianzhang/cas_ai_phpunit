<?php

declare(strict_types=1);

namespace Hausformat\Lib\Tests\Unit\Utility;

use Hausformat\Lib\Utility\XmlToArrayUtility;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class XmlToArrayUtilityTest extends UnitTestCase
{
    #[Test]
    public function convertExtractsSingleStringValueUsingXpath(): void
    {
        $xml = '<root><title>Hello</title></root>';
        $settings = ['title' => 'string(//title)'];

        $result = XmlToArrayUtility::convert($xml, $settings);

        $this->assertSame(['title' => 'Hello'], $result);
    }

    #[Test]
    public function convertExtractsMultipleValuesFromNestedXml(): void
    {
        $xml = '<root><name>Alice</name><age>30</age></root>';
        $settings = [
            'name' => 'string(//name)',
            'age'  => 'string(//age)',
        ];

        $result = XmlToArrayUtility::convert($xml, $settings);

        $this->assertSame('Alice', $result['name']);
        $this->assertSame('30', $result['age']);
    }

    #[Test]
    public function convertReturnsEmptyArrayForEmptySettings(): void
    {
        $result = XmlToArrayUtility::convert('<root/>', []);

        $this->assertSame([], $result);
    }

    #[Test]
    public function convertHandlesRepeatingElementsWithQueryAndMap(): void
    {
        $xml = '<root><item><name>A</name></item><item><name>B</name></item></root>';
        $settings = [
            'items' => [
                'query' => '//item',
                'map'   => ['name' => 'string(name)'],
            ],
        ];

        $result = XmlToArrayUtility::convert($xml, $settings);

        $this->assertCount(2, $result['items']);
        $this->assertSame('A', $result['items'][0]['name']);
        $this->assertSame('B', $result['items'][1]['name']);
    }

    #[Test]
    public function convertThrowsInvalidArgumentExceptionWhenQueryIsPresentButMapIsMissing(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        XmlToArrayUtility::convert('<root/>', ['items' => ['query' => '//item']]);
    }
}
