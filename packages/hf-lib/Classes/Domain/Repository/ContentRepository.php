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
 * The default Repository for \Hausformat\Lib\Domain\Model\Content
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class ContentRepository extends \Hausformat\Lib\Domain\Repository\BaseRepository
{

    /**
     * Finds an object matching the given Name.
     *
     * @param      $pid
     * @param bool $getRawQuery
     *
     * @return object The matching object if found, otherwise NULL
     * @api
     */
    public function findAllByPid($pid, $getRawQuery = false)
    {
        return $this->findAllByPidAndColPos($pid, -1, $getRawQuery);
    }

    /**
     * Finds an object from the given Page.
     *
     * @param      $pid
     * @param      $colPos
     * @param bool $getRawQuery
     *
     * @return object The matching object if found, otherwise NULL
     * @api
     */
    public function findAllByPidAndColPos($pid, $colPos = -1, $getRawQuery = false)
    {
        $query = $this->createQueryWithOffsetAndLimit();

        $query->getQuerySettings()->setRespectStoragePage(false);

        $constraints = [];
        if ($pid instanceof \Hausformat\Lib\Domain\Model\Pages) {
            $constraints[] = $query->equals('pid', $pid->getUid());
        } else {
            $constraints[] = $query->equals('pid', $pid);
        }

        if (is_array($colPos)) {
            $colPosConstraint = [];
            foreach ($colPos AS $onePos) {
                $colPosConstraint[] = $query->equals('colPos', $onePos);
            }
            $constraints[] = $query->logicalOr(...$colPosConstraint);
        } elseif ($colPos >= 0) {
            $constraints[] = $query->equals('pid', $pid);
            $constraints[] = $query->equals('colPos', $colPos);
        }

        $query->matching(
            $query->logicalAnd(...$constraints)
        );

        return $query->execute($getRawQuery);
    }

}
