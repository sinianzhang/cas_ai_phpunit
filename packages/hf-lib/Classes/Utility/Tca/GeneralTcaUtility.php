<?php
declare(strict_types=1);

namespace Hausformat\Lib\Utility\Tca;

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
 * General TCA utility methods
 *
 * TODO: Check if this is up to date
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class GeneralTcaUtility
{
    /**
     * Builds the slug field
     *
     * @param array  $fields
     * @param string $eval
     * @param string $separator
     * @param bool   $prefixParentPageSlug
     * @param string $fallbackCharacter
     * @param int    $size
     *
     * @return array
     */
    public static function getSlugField(array $fields = ['name'], string $eval = 'unique', string $separator = '-', bool $prefixParentPageSlug = false, string $fallbackCharacter = '-', int $size = 50): array
    {
        return [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:pages.slug',
            'config' => [
                'type' => 'slug',
                'size' => $size,
                'generatorOptions' => [
                    'fields' => $fields,
                    'fieldSeparator' => $separator,
                    'prefixParentPageSlug' => $prefixParentPageSlug,
                    'replacements' => [
                        '/' => '',
                    ],
                ],
                'fallbackCharacter' => $fallbackCharacter,
                'eval' => $eval,
            ]
        ];
    }

    /**
     * Get Editlock TCA
     *
     * @return array
     */
    public static function getEditlockFieldTca(): array
    {
        return [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:editlock',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                    ],
                ],
            ],
        ];
    }

    /**
     * Get T3ver Label TCA
     *
     * @return array
     */
    public static function getT3verLabelFieldTca()
    {
        return [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 30,
            ],
        ];
    }
}
