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

use TYPO3\CMS\Core\Resource\Enum\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Index\FileIndexRepository;
use TYPO3\CMS\Core\Resource\Index\MetaDataRepository;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

/**
 * Service class for managing file and folder operations in TYPO3, including file creation, retrieval, and updates.
 * Utilizes various TYPO3 services like ResourceFactory and FolderService to handle file uploads and metadata management.
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class FileService
{
    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper
     *
     */
    protected $dataMapper;

    /**
     * @var FileIndexRepository
     */
    protected $fileIndexRepository;

    /**
     * @var FolderService
     */
    protected $folderService;

    /**
     * @var MetaDataRepository
     */
    protected $metaDataRepository;


    /**
     * @var ResourceFactory
     */
    protected $resourceFactory;

    /**
     * Returns an instance of FileService
     *
     * @return FileService|object
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(self::class);
    }


    /**
     * Creates a file
     *
     * @param string $fileName
     * @param string $folderName
     * @param int $storageId
     * @param DuplicationBehavior $duplicationBehavior
     *
     * @return \TYPO3\CMS\Core\Resource\FileInterface
     * @throws \TYPO3\CMS\Core\Resource\Exception\ExistingTargetFolderException
     * @throws \TYPO3\CMS\Core\Resource\Exception\InsufficientFolderAccessPermissionsException
     * @throws \TYPO3\CMS\Core\Resource\Exception\InsufficientFolderWritePermissionsException
     */
    public function createFile(
        string $fileName,
        string $folderName = 'user_upload',
        $storageId = 1,
        DuplicationBehavior $duplicationBehavior = DuplicationBehavior::RENAME
    ): \TYPO3\CMS\Core\Resource\FileInterface
    {
        $folder = $this->getFolderService()->getOrCreateFolderByPath($folderName, $storageId);
        $temporaryFile = GeneralUtility::tempnam('hf_lib_addfile');

        GeneralUtility::writeFileToTypo3tempDir($temporaryFile, '');

        return $folder->addFile($temporaryFile, $fileName, $duplicationBehavior);
    }

    /**
     * Returns the FolderService
     *
     * @return FolderService
     */
    protected function getFolderService(): FolderService
    {
        if ($this->folderService === null) {
            $this->folderService = GeneralUtility::makeInstance(FolderService::class);
        }

        return $this->folderService;
    }

    /**
     * Returns a file based on the absolute path
     *
     * @param string $path
     *
     * @return \TYPO3\CMS\Core\Resource\ResourceInterface|null
     */
    public function getFileByAbsolutePath(string $path)
    {
        $sitePathLength = strlen(\TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/');
        $relativePath = $path;

        if (substr($path, 0, $sitePathLength) === \TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/') {
            $relativePath = substr($path, $sitePathLength);
        }

        return $this->getFileByPath($relativePath);
    }

    /**
     * Returns a file based on the path
     *
     * @param string $path
     * @param int $storageId
     *
     * @return \TYPO3\CMS\Core\Resource\FileInterface
     */
    public function getFileByPath(string $path, int $storageId = 1)
    {
        $localPath = ltrim($path, '/');
        $storageRepository = GeneralUtility::makeInstance(StorageRepository::class);
        $storage = $storageRepository->getStorageObject($storageId);

        return $storage->getFile($localPath);
    }

    /**
     * Returns the ResourceFactory
     *
     * @return ResourceFactory
     */
    protected function getResourceFactory(): ResourceFactory
    {
        if ($this->resourceFactory === null) {
            $this->resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
        }

        return $this->resourceFactory;
    }

    /**
     * Returns a file based on the path
     *
     * @param string|int $uid
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\File|null
     */
    public function getFileByUid($uid)
    {
        if ($this->fileIndexRepository === null) {
            $this->fileIndexRepository = GeneralUtility::makeInstance(FileIndexRepository::class);
        }

        $fileArray = $this->fileIndexRepository->findOneByUid($uid);

        if ($fileArray !== false) {
            if ($this->dataMapper === null) {
                $this->dataMapper = GeneralUtility::makeInstance(DataMapper::class);
            }

            return $this->dataMapper->map(\TYPO3\CMS\Extbase\Domain\Model\File::class, [$fileArray])[0];
        }

        return null;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper $dataMapper
     */
    public function injectDataMapper(\TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper $dataMapper)
    {
        $this->dataMapper = $dataMapper;
    }


    /**
     * Updates sys_file and sys_file_metadata given a File
     *
     * @param \TYPO3\CMS\Core\Resource\File $file
     */
    public function updateFile(File $file)
    {
        $this->updateMetadata($file->getUid(), $file->getMetaData()->get());
        $this->getFileIndexRepository()->update($file);
    }

    /**
     * Updates the metadatas
     *
     * @param int $uid
     * @param array $metadata
     */
    public function updateMetadata(int $uid, array $metadata)
    {
        $this->getMetaDataRepository()->update($uid, $metadata);
    }

    /**
     * Returns the MetaDataRepository
     *
     * @return MetaDataRepository
     */
    protected function getMetaDataRepository(): MetaDataRepository
    {
        if ($this->metaDataRepository === null) {
            $this->metaDataRepository = GeneralUtility::makeInstance(MetaDataRepository::class);
        }

        return $this->metaDataRepository;
    }

    /**
     * Returns the FileIndexRepository
     *
     * @return FileIndexRepository
     */
    protected function getFileIndexRepository(): FileIndexRepository
    {
        if ($this->fileIndexRepository === null) {
            $this->fileIndexRepository = GeneralUtility::makeInstance(FileIndexRepository::class);
        }

        return $this->fileIndexRepository;
    }

    /**
     * Uploads a file
     *
     * @param array $fileData
     * @param string $folderName
     * @param string|NULL $fileName
     * @param int $storageId
     *
     * @return \TYPO3\CMS\Core\Resource\FileInterface
     * @throws \TYPO3\CMS\Core\Resource\Exception\InsufficientFolderAccessPermissionsException
     */
    public function uploadFile(array $fileData, string $folderName = 'user_upload', ?string $fileName = null, int $storageId = 1)
    {
        $localPath = ltrim($folderName, '/');

        $storageRepository = GeneralUtility::makeInstance(StorageRepository::class);
        $storage = $storageRepository->getStorageObject($storageId);
        $folder = $storage->getFolder($localPath);

        return $storage->addUploadedFile($fileData, $folder, $fileName, DuplicationBehavior::RENAME);
    }
}
