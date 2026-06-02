<?php

declare(strict_types=1);

namespace Hausformat\Lib\Tests\Unit\Utility\Tca;

use Hausformat\Lib\Utility\Tca\GeneralTcaUtility;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class GeneralTcaUtilityTest extends UnitTestCase
{
    #[Test]
    public function getSlugFieldReturnsSlugTypeConfig(): void
    {
        $result = GeneralTcaUtility::getSlugField();

        $this->assertSame('slug', $result['config']['type']);
        $this->assertTrue($result['exclude']);
    }

    #[Test]
    public function getSlugFieldUsesDefaultOptions(): void
    {
        $result = GeneralTcaUtility::getSlugField();

        $this->assertSame(['name'], $result['config']['generatorOptions']['fields']);
        $this->assertSame('-', $result['config']['generatorOptions']['fieldSeparator']);
        $this->assertSame('unique', $result['config']['eval']);
        $this->assertSame(50, $result['config']['size']);
        $this->assertFalse($result['config']['generatorOptions']['prefixParentPageSlug']);
    }

    #[Test]
    public function getSlugFieldAcceptsCustomFieldsAndEval(): void
    {
        $result = GeneralTcaUtility::getSlugField(
            fields: ['title', 'uid'],
            eval: 'uniqueInSite',
            separator: '/',
            prefixParentPageSlug: true,
            fallbackCharacter: '_',
            size: 80
        );

        $this->assertSame(['title', 'uid'], $result['config']['generatorOptions']['fields']);
        $this->assertSame('uniqueInSite', $result['config']['eval']);
        $this->assertSame('/', $result['config']['generatorOptions']['fieldSeparator']);
        $this->assertTrue($result['config']['generatorOptions']['prefixParentPageSlug']);
        $this->assertSame('_', $result['config']['fallbackCharacter']);
        $this->assertSame(80, $result['config']['size']);
    }

    #[Test]
    public function getEditlockFieldTcaReturnsCheckboxToggleConfig(): void
    {
        $result = GeneralTcaUtility::getEditlockFieldTca();

        $this->assertSame('check', $result['config']['type']);
        $this->assertSame('checkboxToggle', $result['config']['renderType']);
        $this->assertTrue($result['exclude']);
    }

    #[Test]
    public function getT3verLabelFieldTcaReturnsInputTypeConfig(): void
    {
        $result = GeneralTcaUtility::getT3verLabelFieldTca();

        $this->assertSame('input', $result['config']['type']);
        $this->assertSame(30, $result['config']['size']);
        $this->assertSame(30, $result['config']['max']);
    }
}
