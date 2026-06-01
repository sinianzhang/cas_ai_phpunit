<?php

namespace Hausformat\ViewHelpers\Tests\Unit\ViewHelpers\Format;

use Hausformat\ViewHelpers\ViewHelpers\Format\CaseViewHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class CaseViewHelperTest extends UnitTestCase
{
    protected CaseViewHelper $viewHelper;

    protected function setUp(): void
    {
        $this->viewHelper = new CaseViewHelper();
    }

    public static function caseTransformationData(): array
    {
        return [
            'camelCase' => ['hallo this is one test', 'camelCase', 'halloThisIsOneTest'],
            'upperCamelCase' => ['another example sentence', 'upperCamelCase', 'AnotherExampleSentence'],
            'upperCase' => ['ärzt groß öl üben', 'upperCase', 'ÄRZT GROSS ÖL ÜBEN'],
            'underscored' => ['helloWorldExample', 'underscored', 'hello_world_example'],
            'lower' => ['HELLO WORLD', 'lower', 'hello world'],
        ];
    }

    #[DataProvider('caseTransformationData')]
    #[Test]
    public function renderTransformsCaseCorrectly(string $value, string $case, string $expected): void
    {
        $arguments = [
            'value' => $value,
            'case' => $case,
        ];
        $this->viewHelper->setArguments($arguments);
        $result = $this->viewHelper->render();
        $this->assertEquals($expected, $result);
    }

    // expect InvalidArgumentException
    #[Test]
    public function renderThrowsExceptionForInvalidCase(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $arguments = [
            'value' => 'someValue',
            'case' => 'invalidCaseType', // An unsupported case
        ];
        $this->viewHelper->setArguments($arguments);
        $this->viewHelper->render();
    }

    // <hf:case case="lower">MIXED INPUT</hf:case> should use renderChildren when value is empty
    #[Test]
    public function renderUsesRenderChildrenWhenValueIsEmpty(): void
    {
        $viewHelper = $this->getMockBuilder(CaseViewHelper::class)
            ->onlyMethods(['renderChildren'])
            ->getMock();

        $viewHelper->expects(self::once())
            ->method('renderChildren')
            ->willReturn('Mixed Input');

        $arguments = [
            'value' => '',
            'case' => 'lower',
        ];

        $viewHelper->setArguments($arguments);
        $result = $viewHelper->render();

        $this->assertSame('mixed input', $result);
    }
}
