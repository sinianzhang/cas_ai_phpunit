<?php

declare(strict_types=1);

namespace Hausformat\Lib\Tests\Unit\Utility\Tca;

use Hausformat\Lib\Utility\Tca\EnableFieldsTcaUtility;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class EnableFieldsTcaUtilityTest extends UnitTestCase
{
    #[Test]
    public function getHiddenFieldTcaReturnsCheckboxToggleConfig(): void
    {
        $result = EnableFieldsTcaUtility::getHiddenFieldTca();

        $this->assertSame('check', $result['config']['type']);
        $this->assertSame('checkboxToggle', $result['config']['renderType']);
        $this->assertTrue($result['exclude']);
    }

    #[Test]
    public function getHiddenFieldTcaDefaultsToInvertedStateDisplayWithVisibleLabel(): void
    {
        $result = EnableFieldsTcaUtility::getHiddenFieldTca();

        $this->assertTrue($result['config']['items'][0]['invertStateDisplay']);
        $this->assertStringContainsString('visible', $result['label']);
    }

    #[Test]
    public function getHiddenFieldTcaWithFalseInvertUsesHiddenLabel(): void
    {
        $result = EnableFieldsTcaUtility::getHiddenFieldTca(false);

        $this->assertFalse($result['config']['items'][0]['invertStateDisplay']);
        $this->assertStringContainsString('hidden', $result['label']);
    }

    #[Test]
    public function getStarttimeFieldTcaReturnsDatetimeTypeConfig(): void
    {
        $result = EnableFieldsTcaUtility::getStarttimeFieldTca();

        $this->assertSame('datetime', $result['config']['type']);
        $this->assertTrue($result['exclude']);
        $this->assertSame('exclude', $result['l10n_mode']);
    }

    #[Test]
    public function getEndtimeFieldTcaReturnsDatetimeTypeConfig(): void
    {
        $result = EnableFieldsTcaUtility::getEndtimeFieldTca();

        $this->assertSame('datetime', $result['config']['type']);
        $this->assertTrue($result['exclude']);
        $this->assertSame('exclude', $result['l10n_mode']);
    }

    #[Test]
    public function getEditlockFieldTcaDelegatesToGeneralTcaUtility(): void
    {
        $result = EnableFieldsTcaUtility::getEditlockFieldTca();

        $this->assertSame('check', $result['config']['type']);
        $this->assertSame('checkboxToggle', $result['config']['renderType']);
    }

    #[Test]
    public function getT3verLabelFieldTcaDelegatesToGeneralTcaUtility(): void
    {
        $result = EnableFieldsTcaUtility::getT3verLabelFieldTca();

        $this->assertSame('input', $result['config']['type']);
        $this->assertSame(30, $result['config']['size']);
    }
}
