<?php

namespace Hausformat\Lib\Utility\Cache;

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

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ClearCache utility methods
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class ClearCacheUtility
{
    /**
     * Alias for clear all Cache
     *
     * @return void
     */
    public static function clearAllCache()
    {
        self::clearCache('all');
    }

    /**
     * This Methode clears all Caches, but you can submit an integer or the values 'pages', 'all' or 'system' for clearing specific caches.
     * You can give all Cachetags (starts with 'cachetag:' or every PageUid)
     *
     * @param int|string $clearType
     *
     * @return void
     */
    public static function clearCache($clearType = 'all')
    {
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->admin = true;
        $removeBEUser = false;
        if (!isset($dataHandler->BE_USER) || $dataHandler->BE_USER == null) {
            $removeBEUser = true;
            $dataHandler->BE_USER = GeneralUtility::makeInstance(BackendUserAuthentication::class);
        }

        $dataHandler->clear_cacheCmd($clearType);

        if ($removeBEUser) {
            unset($dataHandler->BE_USER);
        }
    }

    /**
     * Alias for clear System-Cache
     *
     * @param string $cacheTag
     *
     * @return void
     */
    public static function clearCacheTag($cacheTag)
    {
        if (!str_starts_with(strtolower($cacheTag), 'cachetag:')) {
            $cacheTag = 'cachetag:' . $cacheTag;
        }

        self::clearCache($cacheTag);
    }

    /**
     * Alias for clear Pages-Cache
     *
     * @return void
     */
    public static function clearPageCache()
    {
        self::clearCache('pages');
    }
}
