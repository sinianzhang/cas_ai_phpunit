<?php

declare(strict_types=1);

namespace Hausformat\Lib\Tests\Unit\Utility\Tca;

use Hausformat\Lib\Utility\Tca\LanguageTcaUtility;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class LanguageTcaUtilityTest extends UnitTestCase
{
    #[Test]
    public function getSysLanguageUidFieldTcaReturnsLanguageTypeConfig(): void
    {
        $result = LanguageTcaUtility::getSysLanguageUidFieldTca();

        $this->assertSame('language', $result['config']['type']);
        $this->assertTrue($result['exclude']);
    }

    #[Test]
    public function getSysLanguageUidFieldTcaDefaultsToNotReadOnly(): void
    {
        $result = LanguageTcaUtility::getSysLanguageUidFieldTca();

        $this->assertFalse($result['config']['readOnly']);
    }

    #[Test]
    public function getSysLanguageUidFieldTcaCanBeSetToReadOnly(): void
    {
        $result = LanguageTcaUtility::getSysLanguageUidFieldTca(true, true);

        $this->assertTrue($result['config']['readOnly']);
    }

    #[Test]
    public function getL10nParentFieldTcaReturnsSingleSelectConfig(): void
    {
        $result = LanguageTcaUtility::getL10nParentFieldTca('tx_myext_record');

        $this->assertSame('select', $result['config']['type']);
        $this->assertSame('selectSingle', $result['config']['renderType']);
    }

    #[Test]
    public function getL10nParentFieldTcaSetsCorrectForeignTable(): void
    {
        $result = LanguageTcaUtility::getL10nParentFieldTca('tx_myext_record');

        $this->assertSame('tx_myext_record', $result['config']['foreign_table']);
        $this->assertStringContainsString('tx_myext_record', $result['config']['foreign_table_where']);
    }

    #[Test]
    public function getL10nParentFieldTcaForeignTableWhereContainsSysLanguageUidConstraint(): void
    {
        $result = LanguageTcaUtility::getL10nParentFieldTca('tx_myext_record');

        $this->assertStringContainsString('sys_language_uid', $result['config']['foreign_table_where']);
    }

    #[Test]
    public function getL10nDiffSourceFieldTcaReturnsPassthroughConfig(): void
    {
        $result = LanguageTcaUtility::getL10nDiffSourceFieldTca();

        $this->assertSame('passthrough', $result['config']['type']);
        $this->assertSame('', $result['config']['default']);
    }
}
