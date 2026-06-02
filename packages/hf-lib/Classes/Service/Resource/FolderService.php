<?php

namespace Hausformat\Lib\Service\Resource;

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

use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service class for managing folders within TYPO3, including retrieving or creating folders based on a path.
 * Utilizes the ResourceFactory to handle storage and folder operations.
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class FolderService
{
    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceFactory
     */
    protected $resourceFactory;

    /**
     * Returns an instance of FolderService
     *
     * @return FolderService|object
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(self::class);
    }

    /**
     * Returns or create and returns a folder based on a path
     *
     * @param string $path
     * @param int $storageId
     *
     * @return \TYPO3\CMS\Core\Resource\Folder
     * @throws \TYPO3\CMS\Core\Resource\Exception\ExistingTargetFolderException
     * @throws \TYPO3\CMS\Core\Resource\Exception\InsufficientFolderAccessPermissionsException
     * @throws \TYPO3\CMS\Core\Resource\Exception\InsufficientFolderWritePermissionsException
     */
    public function getOrCreateFolderByPath($path, $storageId = 0)
    {
        $localPath = $path;
        $folder = $this->getFolderByPath($localPath, $storageId);

        if ($folder !== null) {
            return $folder;
        }

        $localPath = $path;
        $storage = $this->getStorage($localPath, $storageId);

        return $storage->createFolder($localPath);
    }

    /**
     * Returns a folder bases on a path
     *
     * @param string $path
     * @param int $storageId
     *
     * @return \TYPO3\CMS\Core\Resource\Folder|null
     * @throws \TYPO3\CMS\Core\Resource\Exception\InsufficientFolderAccessPermissionsException
     */
    public function getFolderByPath($path, $storageId = 0)
    {
        $storage = $this->getStorage($path, $storageId);

        if ($storage->hasFolder($path)) {
            return $storage->getFolder($path);
        }

        if ($storage->hasFolder(urldecode($path))) {
            return $storage->getFolder(urldecode($path));
        }

        return null;
    }

    /**
     * Returns the ResourceStorage
     *
     * @param string $path
     * @param int    $storageId
     * @param-out string|null $path
     *
     * @return \TYPO3\CMS\Core\Resource\ResourceStorage
     */
    protected function getStorage(string &$path, int $storageId = 1)
    {
        $path = ltrim($path, '/');
        $storageRepository = GeneralUtility::makeInstance(StorageRepository::class);
        return $storageRepository->getStorageObject($storageId);
    }

    /**
     * @param \TYPO3\CMS\Core\Resource\ResourceFactory $resourceFactory
     */
    public function injectResourceFactory(\TYPO3\CMS\Core\Resource\ResourceFactory $resourceFactory)
    {
        $this->resourceFactory = $resourceFactory;
    }
}
