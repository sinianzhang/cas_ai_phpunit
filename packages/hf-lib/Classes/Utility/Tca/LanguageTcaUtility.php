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
 * TCA language utility methods
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class LanguageTcaUtility
{

    /**
     * @param $allowAllLanguages @deprecated this parameter has no effect any more and will be removed in the next major version
     * @param $readOnly
     *
     * @return array
     */
    public static function getSysLanguageUidFieldTca($allowAllLanguages = true, $readOnly = false): array
    {
        return [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
                'readOnly' => $readOnly,
            ],
        ];
    }

    /**
     * Get L10n Parent TCA
     *
     * @param string $tableName
     *
     * @return array
     */
    public static function getL10nParentFieldTca(string $tableName): array
    {
        return [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => '', 'value' => 0],
                ],
                'foreign_table' => $tableName,
                'foreign_table_where' => sprintf(
                    // TODO: is this up to date?
                    'AND %1$s.pid=###CURRENT_PID### AND %1$s.sys_language_uid IN (-1,0)',
                    $tableName
                ),
                'default' => 0,
            ],
        ];
    }

    /**
     * Get Passthrough TCA
     *
     * @return array[]
     */
    public static function getL10nDiffSourceFieldTca(): array
    {
        return [
            'config' => [
                'type' => 'passthrough',
                'default' => '',
            ],
        ];
    }
}
