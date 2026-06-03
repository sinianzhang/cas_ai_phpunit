<?php
declare(strict_types=1);

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
 * Case transformation utility methods
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class CaseTransformationUtility
{
    /**
     * @param string $value
     * @param string $case
     *
     * @return string
     */
    public static function transform(string $value, string $case): string
    {
        switch (strtolower($case)) {
            case 'lower':
            case 'lowercase':
                return strtolower($value);
            case 'upper':
            case 'uppercase':
                return strtoupper($value);
            case strtolower('PascalCase'):
            case strtolower('UpperCamelCase'):
                return self::transformToPascalCase($value);
            case strtolower('camelCase'):
            case strtolower('lowerCamelCase'):
                return self::transformToCamelCase($value);
            case 'underscored':
            case 'snake_case':
            case strtolower('snakeCase'):
                return self::transformToSnakeCase($value);
            case strtolower('SCREAMING_SNAKE_CASE'):
            case strtolower('screamingSnakeCase'):
                return self::transformToScreamingSnakeCase($value);
            default:
                throw new \InvalidArgumentException(sprintf('Invalid string case %s', $case));
        }
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public static function transformToPascalCase(string $value): string
    {
        return GeneralUtility::underscoredToUpperCamelCase($value);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public static function transformToCamelCase(string $value): string
    {
        return GeneralUtility::underscoredToLowerCamelCase($value);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public static function transformToSnakeCase(string $value): string
    {
        return GeneralUtility::camelCaseToLowerCaseUnderscored($value);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public static function transformToScreamingSnakeCase(string $value): string
    {
        return strtoupper(GeneralUtility::camelCaseToLowerCaseUnderscored($value));
    }
}
