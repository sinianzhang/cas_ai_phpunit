<?php

namespace Hausformat\Lib\Domain\Repository;


use Hausformat\Lib\Domain\Model\Category;
use Hausformat\Lib\Domain\Repository\Interfaces\TreeableRepositoryInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Sacha P. Suter <support@hausformat.com>, hausformat gmbh
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * The default Repository for \Hausformat\Lib\Domain\Model\Category
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class CategoryRepository extends BaseRepository implements TreeableRepositoryInterface
{

    /**
     * Initializes the repository.
     *
     * @return void
     */
    public function initializeObject()
    {
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings $querySettings */
        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * @param bool $returnRawQuery
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAllRootObjects($returnRawQuery = false)
    {
        return $this->findAllByParent(0, $returnRawQuery);
    }

    /**
     * @param int|string|Category $parent
     * @param bool $returnRawQuery
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAllByParent($parent, $returnRawQuery = false)
    {
        if ($parent instanceof Category) {
            $parentUid = $parent->getUid();
        } else {
            $parentUid = $parent;
        }

        $query = $this->createQuery();
        $this->setGeneralQuerySettingsFromTS($query);

        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->setOrderings(['sorting' => 'ASC']);

        $constraint[] = $query->equals('parent', $parentUid);
        $query->matching($query->logicalAnd(...$constraint));

        return $query->execute($returnRawQuery);
    }

    /**
     * @param bool $returnRawQuery
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAllRootCategories($returnRawQuery = false)
    {
        return $this->findAllByParent(0, $returnRawQuery);
    }

}
