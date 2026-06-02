<?php

namespace Hausformat\Lib\Service\Interfaces;

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

use Hausformat\Lib\Domain\Model\FrontendUser;
use Hausformat\Lib\Domain\Model\FrontendUserGroup;

/**
 * Interface for FE-User Service Classes
 *
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
interface FrontendUserServiceInterface
{

    /**
     *
     * Returns the Teacher witch is Loggedin, otherwise it returns FALSE
     *
     * @return NULL|\TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getLoginFeGroups();

    /**
     *
     * Returns the FE-User witch is Loggedin, otherwise it returns NULL
     *
     * @return NULL|FrontendUser
     */
    public function getLoginFeUser();

    /**
     *
     * return the Uid of FeUser witch is Loggedin
     *
     * @return bool|int
     */
    public function getLoginFeUserUid();

    /**
     *
     * Returns the Teacher witch is Loggedin, otherwise it returns FALSE
     *
     * @param $feGroup int|FrontendUserGroup
     *
     * @return bool
     */
    public function hasLoginFeGroup($feGroup);

    /**
     *
     * Checks if a FeUser is Loggedin
     *
     * @return bool
     */
    public function isFeUserLogin();

}
