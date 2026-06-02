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

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Standard model for FE user groups in HF extensions.
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class FrontendUserGroup extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity implements \JsonSerializable
{
    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var ObjectStorage<\Hausformat\Lib\Domain\Model\FrontendUserGroup>
     */
    protected $subgroup;

    /**
     * Constructs a new Frontend User Group
     *
     * @param string $title
     */
    public function __construct($title = '')
    {
        $this->setTitle($title);
        $this->subgroup = new ObjectStorage();
    }

    /**
     * Sets the title value
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the title value
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the description value
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Returns the description value
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the subgroups. Keep in mind that the property is called "subgroup"
     * although it can hold several subgroups.
     *
     * @param ObjectStorage<\Hausformat\Lib\Domain\Model\FrontendUserGroup> $subgroup An object storage containing the subgroups to add
     */
    public function setSubgroup(ObjectStorage $subgroup)
    {
        $this->subgroup = $subgroup;
    }

    /**
     * Adds a subgroup to the frontend user
     *
     * @param FrontendUserGroup $subgroup
     */
    public function addSubgroup(\Hausformat\Lib\Domain\Model\FrontendUserGroup $subgroup)
    {
        $this->subgroup->attach($subgroup);
    }

    /**
     * Removes a subgroup from the frontend user group
     *
     * @param FrontendUserGroup $subgroup
     */
    public function removeSubgroup(\Hausformat\Lib\Domain\Model\FrontendUserGroup $subgroup)
    {
        $this->subgroup->detach($subgroup);
    }

    /**
     * Returns the subgroups. Keep in mind that the property is called "subgroup"
     * although it can hold several subgroups.
     *
     * @return ObjectStorage<\Hausformat\Lib\Domain\Model\FrontendUserGroup> An object storage containing the subgroups
     */
    public function getSubgroup()
    {
        return $this->subgroup;
    }

    public function jsonSerialize(): array
    {
        return $this->_getProperties();
    }
}
