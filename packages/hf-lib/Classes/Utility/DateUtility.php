<?php

namespace Hausformat\Lib\Utility;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Date utility methods
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class DateUtility
{
    /**
     * Returns a normalized year
     *
     * if the year isn't four chars long, everything below 80 will be added to 2000,
     * everything over 80 will be added to 1900
     *
     * @param int $year
     *
     * @return int four chars long
     */
    public static function normalizeYear($year)
    {
        $year = (int)$year;

        if (MathUtility::isIntegerInRange($year, 70, 99)) {
            return 1900 + $year;
        }

        if (MathUtility::isIntegerInRange($year, 0, 70)) {
            return 2000 + $year;
        }

        return $year;
    }

    /**
     * helper to format a datetime with a strftime string
     *
     * @see http://de.php.net/manual/de/function.strftime.php
     *
     * @param string $format
     * @param \DateTime $dateTime
     *
     * @return string
     */
    public static function strftimeDateTime($format, \DateTime $dateTime)
    {
        return strftime($format, $dateTime->getTimestamp());
    }

    /**
     * Returns a dateTime object from a given week (0 - 52)
     *
     * @param int $week
     * @param int $dayOfWeek
     * @param int $year
     *
     * @return \DateTime
     */
    public static function weekToDateTime($week, $dayOfWeek = null, $year = null)
    {
        if ($year === null) {
            $year = date('Y');
        }

        $week = intval($week);

        if (1 > $week) {
            $week = 1;
        }

        // sanitize input
        $year += floor(($week - 1) / 52);
        $week = ($week - 1) % 52 + 1;

        $date = $year . '-W' . str_pad("$week", 2, '0', STR_PAD_LEFT);

        if ($dayOfWeek !== null) {
            $date .= '-' . max(0, min(7, intval($dayOfWeek)));
        }

        $timestamp = strtotime($date);

        if ($timestamp) {
            $dateTime = \DateTime::createFromFormat('Ymd', date('Ymd', $timestamp))->setTime(12, 00, 00);
        } else {
            $dateTime = null;
        }

        return $dateTime;
    }
}
