<?php

namespace Hausformat\Lib\Utility\Resource;

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

use Hausformat\Lib\Service\Resource\FolderService;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Type\File\FileInfo;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Folder utility methods
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class FolderUtility
{
    /**
     * @param $path
     * @param int $storageId
     * @return null|\TYPO3\CMS\Core\Resource\Folder
     * @throws \TYPO3\CMS\Core\Resource\Exception\ExistingTargetFolderException
     * @throws \TYPO3\CMS\Core\Resource\Exception\InsufficientFolderAccessPermissionsException
     * @throws \TYPO3\CMS\Core\Resource\Exception\InsufficientFolderWritePermissionsException
     */
    public static function getFolderByPath($path, $storageId = 0)
    {
        $path = urldecode($path);

        if (strpos($path, 't3://folder?') === 0) {
            $path = str_replace('t3://folder?', '', $path);
        }

        $pathArray = explode('&', $path);

        if (count($pathArray) > 1) {
            $storageInfo = array_shift($pathArray);
            $storageArray = explode('=', $storageInfo);

            if (count($storageArray) > 1) {
                $storageId = $storageArray[1];
            } else {
                $storageId = $storageArray[0];
            }
        }

        $identifierArray = explode('=', $pathArray[0]);

        if (count($identifierArray) > 1) {
            $identifier = $identifierArray[1];
        } else {
            $identifier = $identifierArray[0];
        }

        return FolderService::getInstance()->getOrCreateFolderByPath($identifier, $storageId);
    }

    /**
     * @param Folder $folder
     *
     * @return FileInfo
     */
    public static function getFolderInfo(Folder $folder)
    {
        $config = $folder->getStorage()->getConfiguration();
        $basePath = $config['basePath'];

        if ($config['pathType'] === 'relative') {
            $basePath = GeneralUtility::getFileAbsFileName($basePath);
        }

        $path = $basePath . $folder->getReadablePath();

        return GeneralUtility::makeInstance(FileInfo::class, $path);
    }
}
