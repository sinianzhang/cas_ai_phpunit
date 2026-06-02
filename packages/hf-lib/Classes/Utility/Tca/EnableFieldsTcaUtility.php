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
 * TCA enable fields utility methods
 *
 * TODO: Check if this is up to date
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class EnableFieldsTcaUtility
{
    /**
     * Get Hidden Field TCA
     *
     * @param bool $invertStateDisplay
     *
     * @return array
     */
    public static function getHiddenFieldTca(bool $invertStateDisplay = true): array
    {
        return [
            'exclude' => true,
            'label' => self::getHiddenFieldLabel($invertStateDisplay),
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        'label' => '',
                        'value' => '',
                        'invertStateDisplay' => $invertStateDisplay,
                    ],
                ],
            ],
        ];
    }

    private static function getHiddenFieldLabel(bool $invertStateDisplay): string
    {
        if ($invertStateDisplay) {
            return 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible';
        } else {
            return 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden';
        }
    }

    /**
     * Get Starttime Field TCA
     *
     * @return array
     */
    public static function getStarttimeFieldTca(): array
    {
        return [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'datetime',
                'eval' => 'int',
                'default' => 0,
            ],
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
        ];
    }

    /**
     * Get Endtime Field TCA
     *
     * @return array
     */
    public static function getEndtimeFieldTca(): array
    {
        return [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'datetime',
                'eval' => 'int',
                'default' => 0,
            ],
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
        ];
    }

    /**
     * @deprecated Use GeneralTcaUtility::getEditlockFieldTca() instead
     * @return array
     */
    public static function getEditlockFieldTca(): array
    {
        return GeneralTcaUtility::getEditlockFieldTca();
    }

    /**
     * @deprecated Use GeneralTcaUtility::getT3verLabelFieldTca() instead
     * @return array
     */
    public static function getT3verLabelFieldTca()
    {
        return GeneralTcaUtility::getT3verLabelFieldTca();
    }
}
