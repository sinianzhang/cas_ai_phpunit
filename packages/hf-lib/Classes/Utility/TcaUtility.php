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

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * TCA utility methods
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class TcaUtility
{

    /**
     * Moves a field that is already added to a new position
     *
     * @param string $table
     * @param string $newFieldsString
     * @param string $typeList
     * @param string $position
     */
    public static function moveInAllTcaTypes($table, $newFieldsString, $typeList = '', $position = '')
    {
        static::removeFromAllTcaTypes($table, $newFieldsString, $typeList);
        ExtensionManagementUtility::addToAllTCAtypes($table, $newFieldsString, $typeList, $position);
    }

    /**
     * Removes a field from all given types
     *
     * @param string $table
     * @param string $newFieldsString
     * @param string $typeList
     */
    public static function removeFromAllTcaTypes($table, $newFieldsString, $typeList = '')
    {
        foreach ($GLOBALS['TCA'][$table]['types'] as $type => &$typeDetails) {
            // skip if we don't want to add the field for this type
            if ($typeList !== '' && !GeneralUtility::inList($typeList, $type)) {
                continue;
            }

            if (!isset($typeDetails['showitem'])) {
                continue;
            }

            $fieldArray = GeneralUtility::trimExplode(',', $typeDetails['showitem'], true);

            if (!in_array($newFieldsString, $fieldArray, true)) {
                continue;
            }

            $typeDetails['showitem'] = implode(
                ',',
                array_diff($fieldArray, [$newFieldsString])
            );
        }
    }
}
