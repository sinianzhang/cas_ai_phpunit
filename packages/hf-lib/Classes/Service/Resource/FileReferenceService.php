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

use Hausformat\Lib\Domain\Model\FileReference;
use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service class for creating and managing file references in TYPO3, linking files to the `sys_file` table.
 * Handles the persistence of file references using the FileReferenceRepository and PersistenceManager.
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class FileReferenceService
{
    /**
     * @var \Hausformat\Lib\Domain\Repository\FileReferenceRepository
     *
     */
    private $fileReferenceRepository;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface
     *
     */
    private $persistenceManager;

    /**
     * Returns an instance of FileReferenceService
     *
     * @return FileReferenceService|object
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(self::class);
    }


    /**
     * Creates a FileReference
     *
     * @param AbstractFile $file
     * @param string $fileReferenceType
     *
     * @return \Hausformat\Lib\Domain\Model\FileReference
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function createFileReference(AbstractFile $file, $fileReferenceType = FileReference::class): FileReference
    {
        /** @var FileReference $fileReference */
        $fileReference = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($fileReferenceType);

        $fileReference->setUidLocal($file->getUid());
        $fileReference->setTableLocal('sys_file');

        $this->fileReferenceRepository->add($fileReference);
        $this->persistenceManager->persistAll();

        return $fileReference;
    }

    /**
     * @param \Hausformat\Lib\Domain\Repository\FileReferenceRepository $fileReferenceRepository
     */
    public function injectFileReferenceRepository(
        \Hausformat\Lib\Domain\Repository\FileReferenceRepository $fileReferenceRepository
    )
    {
        $this->fileReferenceRepository = $fileReferenceRepository;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface $persistenceManager
     */
    public function injectPersistenceManager(\TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }
}
