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

use Hausformat\Lib\Domain\Model\Interfaces\SortableInterface;
use Hausformat\Lib\Domain\Model\Interfaces\TranslatableInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * A generic domain object.
 * All HF-Extension domain model objects must inherit from this AbstractEntity,
 * as it provides the most needed properties and methods.
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
abstract class AbstractEntity extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
    implements TranslatableInterface, SortableInterface, \JsonSerializable
{
    /**
     * @var boolean
     */
    protected $hidden;

    /**
     * @var int
     */
    protected $l10nParent;

    /**
     * @var int
     */
    protected $languageUid = null;

    /**
     * @var int
     */
    protected $localizedUid = null;

    /**
     * @var int $sorting
     */
    protected $sorting = 0;

    /**
     * @return boolean $hidden
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * @return boolean $hidden
     */
    public function isHidden()
    {
        return $this->getHidden();
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
     * @return AbstractEntity
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
     * @return AbstractEntity
     */
    public function setLocalizedUid($value)
    {
        $this->localizedUid = intval($value);
        $this->_localizedUid = intval($value);

        return $this;
    }

    /**
     * getter for sorting
     *
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
     * Returns the "asked" Property or if no Property is defined, tries to change it to lowerCamelCase and return that one.
     *
     * @param $paramName
     *
     * @return mixed
     */
    public function getParameter($paramName)
    {
        $paramValue = $this->_getProperty($paramName);
        if ($paramValue != null) {
            return $paramValue;
        }
        $lowerCamelCaseParam = GeneralUtility::underscoredToLowerCamelCase($paramName);
        if (isset($this->{$lowerCamelCaseParam})) {
            return $this->{$lowerCamelCaseParam};
        }
        return FALSE;
    }

    public function jsonSerialize(): array
    {
        return $this->_getProperties();
    }
}
