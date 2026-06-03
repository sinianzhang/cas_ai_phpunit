<?php

namespace Hausformat\Lib\Domain\Repository;

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

/**
 * The default Repository for \Hausformat\Lib\Domain\Model\FrontendUser
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class FrontendUserRepository extends \Hausformat\Lib\Domain\Repository\BaseRepository
{
    /**
     * Finds an object matching the given Name.
     *
     * @param string $name The name of the FE-User
     *
     * @return int
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @api
     */
    public function countAllByName($name)
    {
        return count($this->findAllByName($name, true));
    }

    /**
     * Finds an object matching the given Name.
     *
     * @param string $name The name of the FE-User
     * @param bool $getRawQuery
     *
     * @return object|array The matching object if found, otherwise NULL
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @api
     */
    public function findAllByName($name, $getRawQuery = false)
    {
        $query = $this->createQuery();
        $querySettings = $query->getQuerySettings();

        if (!is_array($name)) {
            $newNameArray[] = $name;
            $name = $newNameArray;
        }

        $allConstraints = [];

        foreach ($name as $oneNameLine) {
            $nameArray = GeneralUtility::trimExplode(' ', $oneNameLine);
            if ($name == '' || $name == ' ') {
                $allConstraints[] = $query->like('name',
                    'WithAnEmptyStringYouFoundAllUsersButIWantOnlyOneIfThereIsSomethingInsideTheNameString');
            } elseif (count($nameArray) == 1) {
                $allConstraints[] = $query->like('name', $oneNameLine);
            } elseif (count($nameArray) == 2) {
                $constraints1[] = $query->like('firstName', $nameArray[1]);
                $constraints1[] = $query->like('lastName', $nameArray[0]);
                $allConstraints[] = $query->logicalAnd(...$constraints1);

                $constraints2[] = $query->like('firstName', $nameArray[0]);
                $constraints2[] = $query->like('lastName', $nameArray[1]);
                $allConstraints[] = $query->logicalAnd(...$constraints2);

                $allConstraints[] = $query->like('name', $oneNameLine);
            } elseif (count($nameArray) > 2) {
                $constraints1[] = $query->like('firstName', $nameArray[1] . '%');
                $constraints1[] = $query->like('lastName', $nameArray[0]);
                $allConstraints[] = $query->logicalAnd(...$constraints1);

                $constraints2[] = $query->like('firstName', $nameArray[0]);
                $constraints2[] = $query->like('lastName', $nameArray[1] . '%');
                $allConstraints[] = $query->logicalAnd(... $constraints2);

                $constraints3[] = $query->like('firstName', $nameArray[0] . '%');
                $constraints3[] = $query->like('lastName', '%' . $nameArray[2] . '%');
                $allConstraints[] = $query->logicalAnd(...$constraints3);

                $constraints4[] = $query->like('firstName', '%' . $nameArray[2] . '%');
                $constraints4[] = $query->like('lastName', $nameArray[0] . '%');
                $allConstraints[] = $query->logicalAnd(...$constraints4);

                $constraints5[] = $query->like('firstName', '%' . $nameArray[1] . '%');
                $constraints5[] = $query->like('lastName', '%' . $nameArray[2] . '%');
                $allConstraints[] = $query->logicalAnd(...$constraints5);

                $constraints6[] = $query->like('firstName', '%' . $nameArray[2] . '%');
                $constraints6[] = $query->like('lastName', '%' . $nameArray[1] . '%');
                $allConstraints[] = $query->logicalAnd(...$constraints6);

                $allConstraints[] = $query->like('name', $oneNameLine);
            }

        }

        $query->matching(
            $query->logicalOr(...$allConstraints)
        );

        $query->setQuerySettings($querySettings);

        return $query->execute($getRawQuery);

    }

    /**
     * Finds an object matching the given Name.
     *
     * @param string $name The name of the FE-User
     * @param bool $getRawQuery
     *
     * @return object|null The matching object if found, otherwise NULL
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @api
     */
    public function findOneByName($name, $getRawQuery = false)
    {
        if ($name != '') {
            if ($getRawQuery) {
                $retArray = $this->findAllByName($name, true);
                if (count($retArray) >= 1) {
                    return $retArray[0];
                }
            } else {
                /** @var object $retStorage */
                $retStorage = $this->findAllByName($name);
                if ($retStorage->count() >= 1) {
                    return $retStorage->getFirst();
                }
            }
        }

        return null;
    }
}
