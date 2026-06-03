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

/**
 * The default Repository for \Hausformat\Lib\Domain\Model\Pages
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class PagesRepository extends \Hausformat\Lib\Domain\Repository\BaseRepository
{

    /**
     * Finds all sub-Pages by a Page or ID.
     *
     * @param      $pid
     * @param bool $getRawQuery
     *
     * @return object The matching object if found, otherwise NULL
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @api
     */
    public function findAllByParent($pid, $getRawQuery = false)
    {
        $query = $this->createQuery();

        $constraints = [];
        if ($pid instanceof \Hausformat\Lib\Domain\Model\Pages) {
            $constraints[] = $query->in('pid', $pid->getUid());
        } else {
            $constraints[] = $query->in('pid', $pid);
        }

        $query->matching(
            $query->logicalAnd(...$constraints)
        );

        return $query->execute($getRawQuery);
    }

    /**
     * Finds all sub-Pages by a Page or ID.
     *
     * @param \Hausformat\Lib\Domain\Model\Pages $page
     * @param bool $getRawQuery
     *
     * @return object|array The matching object if found, otherwise NULL
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @api
     */
    public function findParent($page, $getRawQuery = false)
    {
        $query = $this->createQuery();

        $constraints = [];
        $constraints[] = $query->in('uid', $page->getPid());

        $query->matching(
            $query->logicalAnd(...$constraints)
        );
        if ($getRawQuery) {
            return $query->execute($getRawQuery)[0];
        } else {
            return $query->execute($getRawQuery)->getFirst();
        }
    }

}
