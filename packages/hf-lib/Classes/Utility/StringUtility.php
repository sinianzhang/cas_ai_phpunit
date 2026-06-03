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

/**
 * String utility methods
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class StringUtility
{
    protected static $phonenumberPattern = '/^((0[0-9]{2})|(\+[0-9]+[ ]*[0-9]+))[ ]*[0-9]{3}[ ]*[0-9]{2}[ ]*[0-9]{2}$/';

    protected static $usernameReplacePattern = '/[^A-Za-z0-9.@\-\_]/';

    protected static $pathReplacePattern = '/[^A-Za-z0-9.\-\_\/]/';

    protected static $globalChangeChars = [
        'specialChars' => [
            "'",
            '"',
            "?",
            "^",
            "|",
            "!",
            "#",
            "{",
            "}",
            "+",
            "`",
            "´",
            "%",
            "$",
            ",",
            ";",
            "*",
            "~",
            "§",
            "[",
            "]",
            "(",
            ")",
            "=",
            "&",
        ],
        'altChars' => [
            "-",
            '',
            "",
            "",
            "-",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            ""
            /*";"*/,
            "",
            "",
            "",
            "",
            "",
            "",
            ""
            /*")"*/,
            "-",
            "/",
            "",
        ],
    ];

    protected static $germanUmlauts = [
        'specialChars' => [
            "ä",
            "ö",
            "ü",
            "Ä",
            "Ö",
            "Ü",
        ],
        'altChars' => [
            "ae",
            "oe",
            "ue",
            "Ae",
            "Oe",
            "Ue",
        ],
    ];

    protected static $germanUmlautsToOneChar = [
        'specialChars' => [
            "ä",
            "ö",
            "ü",
            "Ä",
            "Ö",
            "Ü",
        ],
        'altChars' => [
            "a",
            "o",
            "u",
            "A",
            "O",
            "U",
        ],
    ];

    protected static $twoCharUmlauts = [
        'specialChars' => ['ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü'],
        'altChars' => ['ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü'],
    ];

    protected static $accentChars = [
        'specialChars' => [
            "è",
            "é",
            "ë",
            "à",
            "È",
            "É",
            "Ë",
            "À",
            "À",
            "Á",
            "Â",
            "Ò",
            "Ó",
            "Ô",
            "Ù",
            "Ú",
            "Û",
            "à",
            "á",
            "â",
            "ò",
            "ó",
            "ô",
            "ù",
            "ú",
            "û",
        ],
        'altChars' => [
            "e",
            "e",
            "e",
            "a",
            "E",
            "E",
            "E",
            "A",
            "A",
            "A",
            "A",
            "O",
            "O",
            "O",
            "U",
            "U",
            "U",
            "a",
            "a",
            "a",
            "o",
            "o",
            "o",
            "u",
            "u",
            "u",
        ],
    ];

    protected static $nonLatinChars = [
        'specialChars' => [
            "ß",
            "ç",
        ],
        'altChars' => [
            'ss',
            'c',
        ],
    ];

    protected static $noEMailChars = [
        'specialChars' => [
            "/",
            '\\',
        ],
        'altChars' => [
            '',
            '',
        ],
    ];

    protected static $noPathChars = [
        'specialChars' => [
            "//",
            '\\',
            "@",
        ],
        'altChars' => [
            '/',
            '',
            '-at-',
        ],
    ];

    /**
     * @var array
     */
    private static $filterVarMap = [
        FILTER_VALIDATE_EMAIL => 'email',
        FILTER_VALIDATE_INT => 'int',
        FILTER_VALIDATE_FLOAT => 'float',
        FILTER_VALIDATE_IP => 'ip',
        FILTER_VALIDATE_MAC => 'mac',
        FILTER_VALIDATE_URL => 'url',
    ];

    /**
     * Returns a clean path by exchanging special characters with there unproblematic counterpart
     *
     * @param string $path
     * @param string $whitespaceChar
     *
     * @return string
     */
    public static function sanitizePath(string $path, $whitespaceChar = '_'): string
    {
        $cleanPath = self::sanitizeSpecialChars($path);

        $cleanPath = str_replace(static::$noPathChars['specialChars'], static::$noPathChars['altChars'], $cleanPath);
        $cleanPath = str_replace(' ', $whitespaceChar, $cleanPath);

        return preg_replace(self::$pathReplacePattern, '', $cleanPath);
    }

    /**
     * Replaces special chars (like ', ", ?, !, etc.) with their unproblematic counterpart
     *
     * @param string $string
     *
     * @return mixed|string|string[]
     */
    public static function sanitizeSpecialChars(string $string)
    {
        $cleanString = self::sanitizeNonLatinChars($string);

        return str_replace(
            static::$globalChangeChars['specialChars'],
            static::$globalChangeChars['altChars'],
            $cleanString
        );
    }

    /**
     * @param string $string
     *
     * @return array|mixed|string|string[]
     */
    public static function sanitizeNonLatinChars(string $string)
    {
        $cleanString = self::sanitizeDiacritic($string);

        return str_replace(
            static::$nonLatinChars['specialChars'],
            static::$nonLatinChars['altChars'],
            $cleanString);
    }

    /**
     * @param string $string
     *
     * @return mixed|string|string[]
     */
    public static function sanitizeDiacritic(string $string)
    {
        $cleanString = self::sanitizeUmlauts($string);

        return str_replace(
            static::$accentChars['specialChars'],
            static::$accentChars['altChars'],
            $cleanString);
    }

    /**
     * Sanitizes umlauts (ä => ae etc.)
     *
     * @param string $string
     *
     * @return mixed|string|string[]
     */
    public static function sanitizeUmlauts(string $string)
    {
        $cleanString = self::fixTwoCharsUmlauts($string);

        return str_replace(
            static::$germanUmlauts['specialChars'],
            static::$germanUmlauts['altChars'],
            $cleanString);
    }

    /**
     * Replaces umlauts with their unproblematic counterpart (ä => ae etc.)
     *
     * @param $string
     *
     * @return mixed
     */
    public static function fixTwoCharsUmlauts($string)
    {
        return str_replace(
            self::$twoCharUmlauts['specialChars'],
            self::$twoCharUmlauts['altChars'],
            $string);
    }

    /**
     * Sanitizes an email address
     *
     * @param string $email
     * @param string $whitespaceChar
     *
     * @return string
     */
    public static function sanitizeEMail(string $email, $whitespaceChar = '.'): string
    {
        return static::sanitizeUserName($email, true, $whitespaceChar);
    }

    /**
     * Sanitizes a username
     *
     * @param string $username
     * @param bool $toLowerCase
     * @param string $whitespaceChar
     *
     * @return string
     */
    public static function sanitizeUserName(string $username, bool $toLowerCase, string $whitespaceChar = '-'): string
    {
        $cleanUserName = self::sanitizeSpecialChars($username);

        $cleanUserName = str_replace(static::$noEMailChars['specialChars'], static::$noEMailChars['altChars'], $cleanUserName);
        $cleanUserName = str_replace(' ', $whitespaceChar, $cleanUserName);

        $cleanUserName = preg_replace(self::$usernameReplacePattern, '', $cleanUserName);

        if ($toLowerCase) {
            $cleanUserName = strtolower($cleanUserName);
        }

        return $cleanUserName;
    }

    /**
     * @param string $string
     *
     * @return array|string|string[]
     */
    public static function sanitizeOrderableString(string $string)
    {
        $cleanString = self::sanitizeUmlautsToOneChar($string);
        $cleanString = str_replace(static::$accentChars['specialChars'], static::$accentChars['altChars'], $cleanString);
        return str_replace(static::$nonLatinChars['specialChars'], static::$nonLatinChars['altChars'], $cleanString);
    }

    /**
     * Sanitizes umlauts to one char (ä => a etc.)
     *
     * @param string $string
     *
     * @return mixed|string|string[]
     */
    public static function sanitizeUmlautsToOneChar(string $string)
    {
        $cleanString = self::fixTwoCharsUmlauts($string);

        return str_replace(
            static::$germanUmlautsToOneChar['specialChars'],
            static::$germanUmlautsToOneChar['altChars'],
            $cleanString
        );
    }

    /**
     * Returns the detected type of the given subject-string if possible
     *
     * ### Types
     *
     * - string
     * - boolean
     * - email
     * - int
     * - float
     * - url
     * - ip
     * - mac
     * - phone
     *
     * @param string $subject
     *
     * @return string
     */
    public static function getType($subject)
    {
        if ($subject === '') {
            return 'string'; // HF-TODO: maybe return 'empty'
        }

        foreach (self::$filterVarMap as $const => $type) {
            if (filter_var($subject, $const)) {
                return $type;
            }
        }

        if (filter_var($subject, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null) {
            return 'boolean';
        }

        // Test against swiss phone number regex (accepts +41 062 199 12 19 or 062 199 12 19)
        // HF-TODO: find a better regex
        if (preg_match(self::$phonenumberPattern, $subject)) {
            return 'phone';
        }

        // HF-TODO: Allow URLs without http:// and https://
        if (filter_var('http://' . $subject, FILTER_VALIDATE_URL) && strpos($subject, '.') !== false) {
            return 'url';
        }

        // HF-TODO: maybe unknown type?
        return 'string';
    }

    /**
     * Returns an object, array or string as string if possible (object->toArray() | array->json-formatted-string)
     *
     * @param mixed $subject
     *
     * @return string
     */
    public static function toString($subject)
    {
        if (is_string($subject)) {
            return $subject;
        } else if (is_array($subject)) {
            return json_encode($subject);
        } else if (is_object($subject)) {
            try {
                if (is_callable([$subject, '__toString'])) {
                    return $subject->__toString();
                }
            } catch (\Exception $e) {
            }

            return '';
        }

        return (string) $subject;
    }

    /**
     * Removes whitespaces and linebreaks
     *
     * @param String $expression
     *
     * @return string|string[]
     */
    public static function clearWhiteSpaces(string $expression)
    {
        $order = ["\r\n", "\n", "\r"];

        return str_replace($order, '', $expression);
    }
}
