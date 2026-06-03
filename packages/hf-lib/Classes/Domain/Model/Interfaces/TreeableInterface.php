<?php

namespace Hausformat\Lib\Domain\Model\Interfaces;

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
 * Interface for models organized in a tree structure, allowing for parent-child relationships.
 * Defines methods for managing parent nodes and children collections.
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
interface TreeableInterface
{

    /**
     * @return TreeableInterface
     */
    public function getParent();

    /**
     * @return ObjectStorage
     */
    public function getChildren();

    /**
     * @param ObjectStorage $children
     * @return void
     */
    public function setChildren($children);

    /**
     * @param TreeableInterface $child
     * @return void
     */
    public function addChild($child);

    /**
     * @param TreeableInterface $child
     * @return void
     */
    public function removeChild($child);
}
