<?php

declare(strict_types=1);

namespace Hausformat\Lib\Tests\Unit\Utility;

use Hausformat\Lib\Utility\DateUtility;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class DateUtilityTest extends UnitTestCase
{
    #[Test]
    public function normalizeYearReturnsFourDigitYearUnchanged(): void
    {
        $this->assertSame(2024, DateUtility::normalizeYear(2024));
    }

    #[Test]
    public function normalizeYearAdds1900ForYearsInRange70To99(): void
    {
        $this->assertSame(1985, DateUtility::normalizeYear(85));
        $this->assertSame(1970, DateUtility::normalizeYear(70));
        $this->assertSame(1999, DateUtility::normalizeYear(99));
    }

    #[Test]
    public function normalizeYearAdds2000ForYearsInRange0To69(): void
    {
        $this->assertSame(2024, DateUtility::normalizeYear(24));
        $this->assertSame(2000, DateUtility::normalizeYear(0));
        $this->assertSame(2069, DateUtility::normalizeYear(69));
    }

    #[Test]
    public function weekToDateTimeReturnsDateTimeForValidWeekAndYear(): void
    {
        $result = DateUtility::weekToDateTime(1, null, 2024);

        $this->assertInstanceOf(\DateTime::class, $result);
    }

    #[Test]
    public function weekToDateTimeClampsWeekZeroToWeekOne(): void
    {
        // Arrange
        $resultForWeekOne  = DateUtility::weekToDateTime(1, null, 2024);
        $resultForWeekZero = DateUtility::weekToDateTime(0, null, 2024);

        // Assert
        $this->assertSame($resultForWeekOne->format('Ymd'), $resultForWeekZero->format('Ymd'));
    }

    #[Test]
    public function weekToDateTimeSetsTimeToNoon(): void
    {
        $result = DateUtility::weekToDateTime(5, null, 2024);

        $this->assertSame('12:00:00', $result->format('H:i:s'));
    }

    #[Test]
    public function weekToDateTimeWithDayOfWeekProducesCorrectDate(): void
    {
        // ISO week 1 of 2024, Monday (dayOfWeek=1) → 2024-01-01
        $result = DateUtility::weekToDateTime(1, 1, 2024);

        $this->assertSame('20240101', $result->format('Ymd'));
    }
}
