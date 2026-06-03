<?php

namespace Hausformat\Lib\Service;

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
use Hausformat\Lib\Domain\Repository\FrontendUserGroupRepository;
use Hausformat\Lib\Domain\Repository\FrontendUserRepository;
use Hausformat\Lib\Service\Interfaces\FrontendUserServiceInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service class for managing frontend user operations, including login status checks and retrieval of logged-in users and their groups.
 * Provides utility methods for accessing frontend user data within TYPO3.
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
// HF-ToDO: re-Add Commit: cd51cc4fbae6c45d29c7081ac17b41c21d92a09d (or Revert Commit: 9a961112461e3ce0053c6052405e4477aa217080) for TYPO3 12!
class FrontendUserService implements FrontendUserServiceInterface
{

    /**
     * fegroupRepository
     *
     * @var FrontendUserGroupRepository
     *
     */
    protected $fegroupRepository;

    /**
     * feuserRepository
     *
     * @var FrontendUserRepository
     *
     */
    protected $feuserRepository;

    /**
     * @var FrontendUser
     */
    protected $logedinFeUser = null;

    /**
     * Returns an instance of FrontendUserRepository
     *
     * @return \Hausformat\Lib\Service\FrontendUserService
     */
    public static function getInstance()
    {
        /** @var FrontendUserService $frontendUserService */
        $frontendUserService = GeneralUtility::makeInstance(self::class);
        return $frontendUserService;
    }

    /**
     *
     * Returns the FE-User witch is logged in, otherwise it returns NULL
     *
     * @return NULL|FrontendUser
     * @throws AspectNotFoundException
     */
    public function getLoginFeUser()
    {
        if ($this->isFeUserLogin()) {
            if ($this->logedinFeUser === null) {
                $this->logedinFeUser = $this->feuserRepository->findByUid($this->getLoginFeUserUid());
            }

            return $this->logedinFeUser;
        }

        return null;
    }

    /**
     * Checks if a FeUser is Logged in
     *
     * @return bool
     * @throws AspectNotFoundException
     */
    public function isFeUserLogin()
    {
        $context = GeneralUtility::makeInstance(Context::class);

        return $context->getPropertyFromAspect('frontend.user', 'isLoggedIn', false);
    }

    /**
     * Returns the Uid of FeUser witch is logged in
     *
     * @return bool|int
     * @throws AspectNotFoundException
     */
    public function getLoginFeUserUid()
    {
        if ($this->isFeUserLogin()) {
            $context = GeneralUtility::makeInstance(Context::class);
            return $context->getPropertyFromAspect('frontend.user', 'id');
        }
        return false;
    }

    /**
     * Returns if FE-Group exists
     *
     * @param $feGroup int|FrontendUserGroup
     *
     * @return bool
     * @throws AspectNotFoundException
     */
    public function hasLoginFeGroup($feGroup)
    {
        if (!($feGroup instanceof FrontendUserGroup)) {
            $feGroup = $this->fegroupRepository->findByUid($feGroup);
        }

        if ($this->isFeUserLogin()) {
            $feGroups = $this->getLoginFeGroups();

            if ($feGroups->contains($feGroup)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the logged in FE-Groups
     *
     * @return NULL|\TYPO3\CMS\Extbase\Persistence\ObjectStorage
     * @throws AspectNotFoundException
     */
    public function getLoginFeGroups()
    {
        if ($this->isFeUserLogin()) {
            if ($this->logedinFeUser === null) {
                $this->logedinFeUser = $this->feuserRepository->findByUid($this->getLoginFeUserUid());
            }

            return $this->logedinFeUser->getUsergroup();
        }

        return null;
    }

    /**
     * @param FrontendUserGroupRepository $fegroupRepository
     */
    public function injectFegroupRepository(FrontendUserGroupRepository $fegroupRepository)
    {
        $this->fegroupRepository = $fegroupRepository;
    }

    /**
     * @param FrontendUserRepository $feuserRepository
     */
    public function injectFeuserRepository(FrontendUserRepository $feuserRepository)
    {
        $this->feuserRepository = $feuserRepository;
    }
}
