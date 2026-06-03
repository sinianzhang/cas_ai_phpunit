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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Different utility methods
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class DivUtility
{
    /**
     * Returns the counted lines of a file
     *
     * @param string $path
     *
     * @return int|bool
     */
    public static function countFileLines($path)
    {
        if (!GeneralUtility::isAllowedAbsPath($path)) {
            return false;
        }

        $handle = fopen($path, 'r');
        $count = 0;

        while (fgets($handle)) {
            $count++;
        }

        fclose($handle);

        return $count;
    }

    /**
     * Returns a rgb value based on his hex counterpart
     *
     * @param string $hex
     *
     * @return array|null
     */
    public static function hexToRgb($hex)
    {
        $hex = self::sanitizeHexColor($hex);

        if (!$hex) {
            return null;
        }

        $parts = str_split($hex, 2);

        foreach ($parts as $i => $part) {
            $parts[$i] = hexdec($part);
        }

        return $parts;
    }

    /**
     * Returns the sanitized hex value
     *
     * @param string $hex
     *
     * @return string|bool
     */
    public static function sanitizeHexColor($hex)
    {
        if (strpos($hex, '#') === 0) {
            $hex = substr($hex, 1);
        }

        $length = strlen($hex);

        if ($length !== 6 && $length !== 3) {
            return false;
        }

        if ($length === 3) {
            $parts = str_split($hex);
            $hex = '';

            foreach ($parts as $part) {
                $hex .= $part . $part;
            }
        }

        if (!preg_match('/[a-f0-9A-F]/', $hex)) {
            return false;
        }

        return strtolower($hex);
    }

    /**
     * Returns true if one of the given needles was found in the haystack
     *
     * @param array $needle
     * @param array $haystack
     *
     * @return bool
     */
    public static function inArrayMulti(array $needle, array $haystack)
    {
        foreach ($needle as $element) {
            if (in_array($element, $haystack)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns a hex value based on his rgb counterpart
     *
     * @param int $red
     * @param int $green
     * @param int $blue
     *
     * @return string
     */
    public static function rgbToHex($red, $green, $blue)
    {
        return self::convertToHexPart($red) . self::convertToHexPart($green) . self::convertToHexPart($blue);
    }

    /**
     * Returns a string containing a hexadecimal representation of the given unsigned number argument
     *
     * @param $int
     *
     * @return string
     */
    protected static function convertToHexPart($int)
    {
        $hex = dechex($int);

        if ($int > 15) {
            return $hex;
        }

        return '0' . $hex;
    }
}
