<?php

namespace Hausformat\Lib\Domain\Model;

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

use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Extended FileReference from Extbase Core with frequently used functions and methods for better translation handling.
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class FileReference extends \TYPO3\CMS\Extbase\Domain\Model\FileReference
    implements Interfaces\TranslatableInterface, \JsonSerializable
{
    /**
     * @var string
     */
    protected $alternative;

    /**
     * @var bool
     */
    protected $autoplay;

    /**
     * @var string
     */
    protected $crop;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $downloadname;

    /**
     * fieldname
     *
     * @var string
     */
    protected $fieldname;

    /**
     * @var \TYPO3\CMS\Core\Resource\FileRepository
     */
    protected $fileRepository;

    /**
     * Focus point X
     *
     * @var int
     */
    protected $focusPointX;

    /**
     * Focus point Y
     *
     * @var int
     */
    protected $focusPointY;

    /**
     * @var int
     */
    protected $l10nParent;

    /**
     * @var string
     */
    protected $link;

    /**
     * @var bool
     */
    protected $showinpreview;

    /**
     * @var int $sorting
     */
    protected $sorting = 0;

    /**
     * tableLocal
     *
     * @var string
     */
    protected $tableLocal;

    /**
     * tablenames
     *
     * @var string
     */
    protected $tablenames;

    /**
     * @var string
     */
    protected $title;

    /**
     * tableLocal
     *
     * @var string
     */
    protected $uidForeign;

    /**
     * uidLocal
     *
     * @var int
     */
    protected ?int $uidLocal;

    /**
     * @var bool
     */
    protected $hidden;

    /**
     * Returns the alternative text to this image
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->getAlternative();
    }

    /**
     * Returns the alternative text to this image
     *
     * @return string
     */
    public function getAlternative()
    {
        if ($this->fileRepository == null) {
            $this->fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        }

        return $this->alternative !== null ? $this->alternative : $this->getOriginalResource()->getAlternative();
    }

    /**
     * Set alternative
     *
     * @param string $alternative
     *
     * @return void
     */
    public function setAlternative($alternative)
    {
        $this->alternative = $alternative;
    }

    /**
     * @return mixed
     */
    public function getAutoplay()
    {
        return $this->autoplay;
    }

    /**
     * @param mixed $autoplay
     */
    public function setAutoplay($autoplay)
    {
        $this->autoplay = $autoplay;
    }

    /**
     * Returns the creation time of the file as Unix timestamp
     *
     * @return integer
     */
    public function getCreationTime()
    {
        if ($this->fileRepository == null) {
            $this->fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        }

        return (int)$this->getOriginalResource()->getCreationTime();
    }

    /**
     * @return string
     */
    public function getCrop()
    {
        return $this->crop;
    }

    /**
     * @param string $crop
     */
    public function setCrop($crop)
    {
        $this->crop = $crop;
    }

    /**
     * @return string
     */
    public function getCropDecoded()
    {
        return json_decode($this->crop);
    }

    /**
     * Returns the description text to this file
     *
     * @return string
     */
    public function getDescription()
    {
        if ($this->fileRepository == null) {
            $this->fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        }

        return $this->description !== null ? $this->description : $this->getOriginalResource()->getDescription();
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDownloadname()
    {
        return $this->downloadname;
    }

    /**
     * @param string $downloadname
     */
    public function setDownloadname($downloadname)
    {
        $this->downloadname = $downloadname;
    }

    /**
     * Get the file extension of this file
     *
     * @return string The file extension
     */
    public function getExtension()
    {
        if ($this->fileRepository == null) {
            $this->fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        }

        return $this->getOriginalResource()->getExtension();
    }

    /**
     * @return string
     */
    public function getFieldname()
    {
        return $this->fieldname;
    }

    /**
     * @param string $fieldname
     */
    public function setFieldname($fieldname)
    {
        $this->fieldname = $fieldname;
    }

    /**
     * Get File UID
     *
     * @return int
     */
    public function getFileUid()
    {
        return $this->getUidLocal();
    }

    /**
     * @return int
     */
    public function getUidLocal()
    {
        return $this->uidLocal;
    }

    /**
     * @param int $uidLocal
     */
    public function setUidLocal($uidLocal)
    {
        $this->uidLocal = $uidLocal;
    }

    /**
     * Get X
     *
     * @return int
     */
    public function getFocusPointX()
    {
        return $this->focusPointX;
    }

    /**
     * Set X
     *
     * @param int $focusPointX
     */
    public function setFocusPointX($focusPointX)
    {
        $this->focusPointX = $focusPointX;
    }

    /**
     * Get Y
     *
     * @return int
     */
    public function getFocusPointY()
    {
        return $this->focusPointY;
    }

    /**
     * Set Y
     *
     * @param int $focusPointY
     */
    public function setFocusPointY($focusPointY)
    {
        $this->focusPointY = $focusPointY;
    }

    /**
     * Returns a path to a local version of this file to process it locally (e.g. with some system tool).
     * If the file is normally located on a remote storages, this creates a local copy.
     * If the file is already on the local system, this only makes a new copy if $writable is set to TRUE.
     *
     * @param boolean $writable Set this to FALSE if you only want to do read operations on the file.
     *
     * @return string
     */
    public function getForLocalProcessing($writable = true)
    {
        if ($this->fileRepository == null) {
            $this->fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        }

        return $this->getOriginalResource()->getForLocalProcessing($writable);
    }

    /**
     * @return int
     */
    public function getL10nParent()
    {
        return $this->l10nParent;
    }

    /**
     * @param int $l10nParent
     */
    public function setL10nParent($l10nParent)
    {
        $this->l10nParent = $l10nParent;
    }

    /**
     * @return int
     */
    public function getLanguageUid()
    {
        return $this->_languageUid;
    }

    /**
     * Returns the link that should be active when clicking on this image
     *
     * @return string
     */
    public function getLink()
    {
        if ($this->fileRepository == null) {
            $this->fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        }

        return $this->link !== null ? $this->link : $this->getOriginalResource()->getLink();
    }

    /**
     * Set link
     *
     * @param string $link
     *
     * @return void
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @return int
     */
    public function getLocalizedUid()
    {
        return $this->_localizedUid;
    }

    /**
     * Get the MIME type of this file
     *
     * @return string
     */
    public function getMimeType()
    {
        if ($this->fileRepository == null) {
            $this->fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        }

        return $this->getOriginalResource()->getMimeType();
    }

    /**
     * Returns the modification time of the file as Unix timestamp
     *
     * @return integer
     */
    public function getModificationTime()
    {
        if ($this->fileRepository == null) {
            $this->fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        }

        return (int)$this->getOriginalResource()->getModificationTime();
    }

    /**
     * Returns the name of this file
     *
     * @return string
     */
    public function getName()
    {
        if ($this->fileRepository == null) {
            $this->fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        }

        return $this->getOriginalResource()->getName();
    }

    /**
     * Returns the basename (the name without extension) of this file.
     *
     * @return string
     */
    public function getNameWithoutExtension()
    {
        if ($this->fileRepository == null) {
            $this->fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        }

        return $this->getOriginalResource()->getNameWithoutExtension();
    }

    /**
     * Gets the original file being referenced.
     *
     * @return \TYPO3\CMS\Core\Resource\File
     */
    public function getOriginalFile()
    {
        if ($this->fileRepository == null) {
            $this->fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        }

        return $this->getOriginalResource()->getOriginalFile();
    }

    /**
     * Returns a publicly accessible URL for this file
     *
     *
     * @return string
     */
    public function getPublicUrl()
    {
        if ($this->fileRepository == null) {
            $this->fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        }

        if (!$this->getOriginalResource()->isMissing() && $this->getOriginalResource()->getPublicUrl() !== '') {
            return $this->getOriginalResource()->getPublicUrl();
        } else {
            return '';
        }
    }

    /**
     * Returns the size of this file
     *
     * @return integer
     */
    public function getSize()
    {
        if ($this->fileRepository == null) {
            $this->fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        }

        return (int)$this->getOriginalResource()->getSize();
    }

    /**
     * @return mixed
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @param mixed $sorting
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
    }

    /**
     * @return string
     */
    public function getTableLocal()
    {
        return $this->tableLocal;
    }

    /**
     * @param string $tableLocal
     */
    public function setTableLocal($tableLocal)
    {
        $this->tableLocal = $tableLocal;
    }

    /**
     * @return string
     */
    public function getTablenames()
    {
        return $this->tablenames;
    }

    /**
     * @param string $tablenames
     */
    public function setTablenames($tablenames)
    {
        $this->tablenames = $tablenames;
    }

    /**
     * Returns the title text to this image
     *
     * @return string
     */
    public function getTitle()
    {
        if ($this->fileRepository == null) {
            $this->fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        }

        return $this->title !== null ? $this->title : $this->getOriginalResource()->getTitle();
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the fileType of this file
     *
     * @return integer $fileType
     */
    public function getType()
    {
        if ($this->fileRepository == null) {
            $this->fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        }

        return (int)$this->getOriginalResource()->getType();
    }

    /**
     * @return string
     */
    public function getUidForeign()
    {
        return $this->uidForeign;
    }

    /**
     * @param string $uidForeign
     */
    public function setUidForeign($uidForeign)
    {
        $this->uidForeign = $uidForeign;
    }

    /**
     * Get showinpreview
     *
     * @return bool
     */
    public function isShowinpreview()
    {
        return $this->showinpreview;
    }

    /**
     * Set showinpreview
     *
     * @param bool $showinpreview
     *
     * @return void
     */
    public function setShowinpreview($showinpreview)
    {
        $this->showinpreview = $showinpreview;
    }

    /**
     * Set alternative
     *
     * @param string $alt
     *
     * @return void
     */
    public function setAlt($alt)
    {
        $this->setAlternative($alt);
    }

    /**
     * Set File uid
     *
     * @param int $fileUid
     *
     * @return void
     */
    public function setFileUid($fileUid)
    {
        $this->setUidLocal($fileUid);
    }

    /**
     * @param int $languageUid
     */
    public function setLanguageUid($languageUid)
    {
        $this->_languageUid = $languageUid;
    }

    /**
     * @param int $localizedUid
     */
    public function setLocalizedUid($localizedUid)
    {
        $this->_localizedUid = $localizedUid;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     */
    public function setHidden(bool $hidden): void
    {
        $this->hidden = $hidden;
    }

    /**
     * Returns an array representation of the file.
     * (This is used by the generic listing module vidi when displaying file records.)
     *
     * @return array Array of main data of the file. Don't rely on all data to be present here, it's just a selection of the most relevant information.
     */
    public function toArray()
    {
        if ($this->fileRepository == null) {
            $this->fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        }

        return $this->getOriginalResource()->toArray();
    }

    public function jsonSerialize(): array
    {
        return $this->_getProperties();
    }
}
