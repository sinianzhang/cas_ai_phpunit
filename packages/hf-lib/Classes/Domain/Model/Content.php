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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * A default content model (tt_content) for usage in Extbase
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class Content extends AbstractEntity implements \JsonSerializable
{
    /**
     * bodytext
     *
     * @var string
     *
     */
    protected $bodytext;

    /**
     * CType
     *
     * @var string
     */
    protected $contentType;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category>
     */
    protected $categories;

    /**
     * colPos
     *
     * @var int
     *
     */
    protected $colPos;

    /**
     * colPos
     *
     * @var int
     *
     */
    protected $cols;

    /**
     * header
     *
     * @var string
     *
     */
    protected $header;

    /**
     * headerLink
     *
     * @var string
     *
     */
    protected $headerLink;

    /**
     * header
     *
     * @var string
     *
     */
    protected $headerPosition;

    /**
     * @var boolean
     */
    protected $hidden;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hausformat\Lib\Domain\Model\FileReference>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $image;

    /**
     * Image ratio
     *
     * @var string
     */
    protected $imageRatio;

    /**
     * colPos
     *
     * @var int
     *
     */
    protected $imagecols;

    /**
     * @var int
     */
    protected $imagewidth;

    /**
     * @var int
     */
    protected $imageorient;

    /**
     * @var int
     */
    protected $imageborder;

    /**
     * @var string
     */
    protected $imageZoom;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hausformat\Lib\Domain\Model\FileReference>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $media;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hausformat\Lib\Domain\Model\FileReference>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $assets;

    /**
     * @var string
     */
    protected $layout;

    /**
     * piFlexform
     *
     * @var string
     *
     */
    protected $piFlexform;

    /**
     * sorting
     *
     * @var int
     * @TYPO3\CMS\Extbase\Annotation\Validate(validator="NotEmpty")
     */
    protected $sorting;

    /**
     * subheader
     *
     * @var string
     *
     */
    protected $subheader;

    /**
     * Content constructor.
     */
    public function __construct()
    {
        $this->assets = GeneralUtility::makeInstance(ObjectStorage::class);
        $this->categories = GeneralUtility::makeInstance(ObjectStorage::class);
        $this->media = GeneralUtility::makeInstance(ObjectStorage::class);
        $this->image = GeneralUtility::makeInstance(ObjectStorage::class);
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\Category $category
     */
    public function addCategories($category)
    {
        $this->categories->attach($category);
    }

    /**
     * @return string
     */
    public function getBodytext()
    {
        return $this->bodytext;
    }

    /**
     * @param string $bodytext
     */
    public function setBodytext($bodytext)
    {
        $this->bodytext = $bodytext;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     */
    public function setContentType(string $contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return int
     */
    public function getImagewidth(): int
    {
        return $this->imagewidth;
    }

    /**
     * @param int $imagewidth
     */
    public function setImagewidth(int $imagewidth)
    {
        $this->imagewidth = $imagewidth;
    }

    /**
     * @return int
     */
    public function getImageorient(): int
    {
        return $this->imageorient;
    }

    /**
     * @param int $imageorient
     */
    public function setImageorient(int $imageorient)
    {
        $this->imageorient = $imageorient;
    }

    /**
     * @return int
     */
    public function getImageborder(): int
    {
        return $this->imageborder;
    }

    /**
     * @param int $imageborder
     */
    public function setImageborder(int $imageborder)
    {
        $this->imageborder = $imageborder;
    }

    /**
     * @return string
     */
    public function getImageZoom(): string
    {
        return $this->imageZoom;
    }

    /**
     * @param string $imageZoom
     */
    public function setImageZoom(string $imageZoom)
    {
        $this->imageZoom = $imageZoom;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * @return int
     */
    public function getColPos()
    {
        return $this->colPos;
    }

    /**
     * @param int $colPos
     */
    public function setColPos($colPos)
    {
        $this->colPos = $colPos;
    }

    /**
     * @return int
     */
    public function getCols()
    {
        return $this->cols;
    }

    /**
     * @param int $cols
     */
    public function setCols($cols)
    {
        $this->cols = $cols;
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param string $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getHeaderLink()
    {
        return $this->headerLink;
    }

    /**
     * @param string $headerLink
     */
    public function setHeaderLink($headerLink)
    {
        $this->headerLink = $headerLink;
    }

    /**
     * @return string
     */
    public function getHeaderPosition()
    {
        return $this->headerPosition;
    }

    /**
     * @param string $headerPosition
     */
    public function setHeaderPosition($headerPosition)
    {
        $this->headerPosition = $headerPosition;
    }

    /**
     * @return boolean $hidden
     */
    public function isHidden()
    {
        return $this->getHidden();
    }

    /**
     * @return boolean $hidden
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * @param boolean $hidden
     *
     * @return void
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * @return ObjectStorage
     */
    public function getImage()
    {
        if ($this->image->count() === 0) {
            return $this->getMedia();
        }

        return $this->image;
    }

    /**
     * @param ObjectStorage $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getMedia(): \TYPO3\CMS\Extbase\Persistence\ObjectStorage
    {
        if ($this->media->count() === 0) {
            return $this->getAssets();
        }

        return $this->media;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $media
     */
    public function setMedia(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $media)
    {
        $this->media = $media;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getAssets(): \TYPO3\CMS\Extbase\Persistence\ObjectStorage
    {
        return $this->assets;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $assets
     */
    public function setAssets(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $assets)
    {
        $this->assets = $assets;
    }

    /**
     * Get image ratio
     *
     * @return string
     */
    public function getImageRatio()
    {
        return $this->imageRatio;
    }

    /**
     * Set image ratio
     *
     * @param string $imageRatio
     */
    public function setImageRatio($imageRatio)
    {
        $this->imageRatio = $imageRatio;
    }

    /**
     * @return int
     */
    public function getImagecols()
    {
        return $this->imagecols;
    }

    /**
     * @param int $imagecols
     */
    public function setImagecols($imagecols)
    {
        $this->imagecols = $imagecols;
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param string $layout
     */
    public function setLayout(string $layout)
    {
        $this->layout = $layout;
    }

    /**
     * @return string
     */
    public function getPiFlexform()
    {
        return $this->piFlexform;
    }

    /**
     * @param string $piFlexform
     */
    public function setPiFlexform($piFlexform)
    {
        $this->piFlexform = $piFlexform;
    }

    /**
     * @return int
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @param int $sorting
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
    }

    /**
     * @return string
     */
    public function getSubheader()
    {
        return $this->subheader;
    }

    /**
     * @param string $subheader
     */
    public function setSubheader($subheader)
    {
        $this->subheader = $subheader;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\Category $category
     */
    public function removeCategories($category)
    {
        $this->categories->detach($category);
    }


    /**
     * @return int
     */
    public function getLocalizedUid()
    {
        return $this->_localizedUid;
    }

    /**
     * @param int $localizedUid
     */
    public function setLocalizedUid($localizedUid)
    {
        $this->_localizedUid = $localizedUid;
    }

    public function jsonSerialize(): array
    {
        return $this->_getProperties();
    }
}
