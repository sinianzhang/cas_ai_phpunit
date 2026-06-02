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

/**
 * A default page model (pages) for usage in Extbase.
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class Pages extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity implements \JsonSerializable
{
    /**
     * @var string
     */
    protected $TSconfig;

    /**
     * @var string
     */
    protected $author;

    /**
     * @var string
     */
    protected $authorEmail;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category>
     */
    protected $categories;

    /**
     * @var \Hausformat\Lib\Domain\Model\Pages
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $contentFromPid;

    /**
     * @var boolean
     */
    protected $deleted;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var int
     */
    protected $doktype;

    /**
     * @var int
     */
    protected $editlock;

    /**
     * @var boolean
     */
    protected $hidden;

    /**
     * @var boolean
     */
    protected $isSiteroot;

    /**
     * @var string
     */
    protected $keywords;

    /**
     * @var \Hausformat\Lib\Domain\Model\Pages
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $mountPid;

    /**
     * @var \Hausformat\Lib\Domain\Model\Pages
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $mountPidOl;

    /**
     * @var boolean
     */
    protected $navHide;

    /**
     * @var string
     */
    protected $navTitle;

    /**
     * @var boolean
     */
    protected $noCache;

    /**
     * @var boolean
     */
    protected $phpTreeStop;

    /**
     * @var \Hausformat\Lib\Domain\Model\Pages
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $shortcut;

    /**
     * @var int
     */
    protected $shortcutMode;

    /**
     * @var int
     */
    protected $sorting;

    /**
     * @var \Hausformat\Lib\Domain\Model\Pages
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $storagePid;

    /**
     * @var string
     */
    protected $subtitle;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var int
     */
    protected $urlScheme;

    /**
     * @var int
     */
    protected $urltype;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Hausformat\Lib\Domain\Model\FileReference>
     */
    protected $media;

    /**
     * @var string
     */
    protected $abstract;

    /**
     * @var int
     */
    protected $languageUid = null;

    /**
     * @var int
     */
    protected $localizedUid = null;

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }

    /**
     * @param string $authorEmail
     */
    public function setAuthorEmail($authorEmail)
    {
        $this->authorEmail = $authorEmail;
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
     * @return \Hausformat\Lib\Domain\Model\Pages
     */
    public function getContentFromPid()
    {
        return $this->contentFromPid;
    }

    /**
     * @param \Hausformat\Lib\Domain\Model\Pages $contentFromPid
     */
    public function setContentFromPid($contentFromPid)
    {
        $this->contentFromPid = $contentFromPid;
    }

    /**
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getDoktype()
    {
        return $this->doktype;
    }

    /**
     * @param int $doktype
     */
    public function setDoktype($doktype)
    {
        $this->doktype = $doktype;
    }

    /**
     * @return int
     */
    public function getEditlock()
    {
        return $this->editlock;
    }

    /**
     * @param int $editlock
     */
    public function setEditlock($editlock)
    {
        $this->editlock = $editlock;
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
     * @return boolean
     */
    public function getIsSiteroot()
    {
        return $this->isSiteroot;
    }

    /**
     * @param boolean $isSiteroot
     */
    public function setIsSiteroot($isSiteroot)
    {
        $this->isSiteroot = $isSiteroot;
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param string $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @return \Hausformat\Lib\Domain\Model\Pages
     */
    public function getMountPid()
    {
        return $this->mountPid;
    }

    /**
     * @param \Hausformat\Lib\Domain\Model\Pages $mountPid
     */
    public function setMountPid($mountPid)
    {
        $this->mountPid = $mountPid;
    }

    /**
     * @return mixed
     */
    public function getMountPidOl()
    {
        return $this->mountPidOl;
    }

    /**
     * @param mixed $mountPidOl
     */
    public function setMountPidOl($mountPidOl)
    {
        $this->mountPidOl = $mountPidOl;
    }

    /**
     * @return boolean
     */
    public function getNavHide()
    {
        return $this->navHide;
    }

    /**
     * @param boolean $navHide
     */
    public function setNavHide($navHide)
    {
        $this->navHide = $navHide;
    }

    /**
     * @return string
     */
    public function getNavTitle()
    {
        return $this->navTitle;
    }

    /**
     * @param string $navTitle
     */
    public function setNavTitle($navTitle)
    {
        $this->navTitle = $navTitle;
    }

    /**
     * @return boolean
     */
    public function getNoCache()
    {
        return $this->noCache;
    }

    /**
     * @param boolean $noCache
     */
    public function setNoCache($noCache)
    {
        $this->noCache = $noCache;
    }

    /**
     * @return boolean
     */
    public function getPhpTreeStop()
    {
        return $this->phpTreeStop;
    }

    /**
     * @param boolean $phpTreeStop
     */
    public function setPhpTreeStop($phpTreeStop)
    {
        $this->phpTreeStop = $phpTreeStop;
    }

    /**
     * @return \Hausformat\Lib\Domain\Model\Pages
     */
    public function getShortcut()
    {
        return $this->shortcut;
    }

    /**
     * @param \Hausformat\Lib\Domain\Model\Pages $shortcut
     */
    public function setShortcut($shortcut)
    {
        $this->shortcut = $shortcut;
    }

    /**
     * @return int
     */
    public function getShortcutMode()
    {
        return $this->shortcutMode;
    }

    /**
     * @param int $shortcutMode
     */
    public function setShortcutMode($shortcutMode)
    {
        $this->shortcutMode = $shortcutMode;
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
     * @return \Hausformat\Lib\Domain\Model\Pages
     */
    public function getStoragePid()
    {
        return $this->storagePid;
    }

    /**
     * @param \Hausformat\Lib\Domain\Model\Pages $storagePid
     */
    public function setStoragePid($storagePid)
    {
        $this->storagePid = $storagePid;
    }

    /**
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * @param string $subtitle
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
    }

    /**
     * @return string
     */
    public function getTSconfig()
    {
        return $this->TSconfig;
    }

    /**
     * @param string $TSconfig
     */
    public function setTSconfig($TSconfig)
    {
        $this->TSconfig = $TSconfig;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return int
     */
    public function getUrlScheme()
    {
        return $this->urlScheme;
    }

    /**
     * @param int $urlScheme
     */
    public function setUrlScheme($urlScheme)
    {
        $this->urlScheme = $urlScheme;
    }

    /**
     * @return int
     */
    public function getUrltype()
    {
        return $this->urltype;
    }

    /**
     *
     * @param int $urltype
     */
    public function setUrltype($urltype)
    {
        $this->urltype = $urltype;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $media
     */
    public function setMedia($media)
    {
        $this->media = $media;
    }

    /**
     * @return string
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * @param string $abstract
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;
    }

    /**
     * getter for languageUid
     *
     * @return int
     */
    public function getLanguageUid()
    {
        if ($this->languageUid == null && $this->_languageUid !== null) {
            $this->languageUid = $this->_languageUid;
        }

        return $this->languageUid;
    }

    /**
     * setter for languageUid
     *
     * @param int $value
     *
     * @return \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
     */
    public function setLanguageUid($value)
    {
        $this->languageUid = intval($value);
        $this->_languageUid = intval($value);

        return $this;
    }

    /**
     * getter for localizedUid
     *
     * @return int
     */
    public function getLocalizedUid()
    {
        if ($this->localizedUid == null && $this->_localizedUid !== null) {
            $this->localizedUid = $this->_localizedUid;
        }

        return $this->localizedUid;
    }

    /**
     * setter for localizedUid
     *
     * @param int $value
     *
     * @return \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
     */
    public function setLocalizedUid($value)
    {
        $this->localizedUid = intval($value);
        $this->_localizedUid = intval($value);

        return $this;
    }

    public function jsonSerialize(): array
    {
        return $this->_getProperties();
    }
}
