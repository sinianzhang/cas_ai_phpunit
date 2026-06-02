<?php

namespace Hausformat\Lib\Service;

/*                                                                      *
 *  COPYRIGHT NOTICE                                                    *
 *                                                                      *
 *  (c) 2010 Sacha P. Suter <support@hausformat.com>                               *
 *           .hausformat                                              *
 *           All rights reserved                                        *
 *                                                                      *
 *  This script is part of the TYPO3 project. The TYPO3 project is      *
 *  free software; you can redistribute it and/or modify                *
 *  it under the terms of the GNU General Public License as published   *
 *  by the Free Software Foundation; either version 2 of the License,   *
 *  or (at your option) any later version.                              *
 *                                                                      *
 *  The GNU General Public License can be found at                      *
 *  http://www.gnu.org/copyleft/gpl.html.                               *
 *                                                                      *
 *  This script is distributed in the hope that it will be useful,      *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of      *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the       *
 *  GNU General Public License for more details.                        *
 *                                                                      *
 *  This copyright notice MUST APPEAR in all copies of the script!      *
 *                                                                      */

use Hausformat\Lib\Domain\Model\Category;
use Hausformat\Lib\Domain\Model\Interfaces\TreeableInterface;
use Hausformat\Lib\Domain\Repository\CategoryRepository;
use Hausformat\Lib\Domain\Repository\Interfaces\TreeableRepositoryInterface;
use Hausformat\Lib\Utility\ObjectStorageUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentTypeException;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Service class for managing tree-structured entities, providing methods to create trees and subtrees from repository data.
 * Supports dynamic tree creation for entities implementing the TreeableInterface, with configurable maximum depth.
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class TreeableService implements SingletonInterface
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @param \Hausformat\Lib\Domain\Repository\CategoryRepository $categoryRepository
     */
    public function injectCategoryRepository(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param TreeableRepositoryInterface|null $treeableRepository
     * @param int $maxDepth
     * @return ObjectStorage
     * @throws InvalidArgumentTypeException
     */
    public function createTreeFromRoot(?TreeableRepositoryInterface $treeableRepository = null, int $maxDepth = 99)
    {
        if ($treeableRepository === null) {
            $treeableRepository = $this->categoryRepository;
        }
        $treeableObjects = $treeableRepository->findAllRootObjects();
        $treeableObjects = ObjectStorageUtility::getOjectStorageFromQueryResult($treeableObjects);

        return $this->createTree($treeableObjects, $treeableRepository, $maxDepth);
    }

    /**
     * @param ObjectStorage $treeables
     * @param TreeableRepositoryInterface|null $treeableRepository
     * @param int $maxDepth
     * @return ObjectStorage
     * @throws InvalidArgumentTypeException
     */
    public function createTree(ObjectStorage $treeables, ?TreeableRepositoryInterface $treeableRepository = null, int $maxDepth = 99)
    {
        /** @var TreeableInterface $treeable */
        foreach ($treeables AS $treeable) {
            $this->createSubTree($treeable, $treeableRepository, $maxDepth);
        }
        return $treeables;
    }

    /**
     * @param TreeableInterface $treeable
     * @param TreeableRepositoryInterface|null $treeableRepository
     * @param int $maxDepth
     * @return TreeableInterface
     * @throws InvalidArgumentTypeException
     */
    public function createSubTree(TreeableInterface $treeable, ?TreeableRepositoryInterface $treeableRepository = null, int $maxDepth = 99)
    {
        if ($maxDepth === 0) {
            return $treeable;
        }
        if ($treeableRepository === null && $treeable instanceof Category) {
            $treeableRepository = $this->categoryRepository;
        } elseif ($treeableRepository === null) {
            throw new InvalidArgumentTypeException('$treeable is not an instance of ' . Category::class . ' and no $treeableRepository is submitted.');
        }

        $childObjects = $treeableRepository->findAllByParent($treeable);

        $maxDepth--;
        foreach ($childObjects AS $childObject) {
            $childObject = $this->createSubTree($childObject, $treeableRepository, $maxDepth);
            $treeable->addChild($childObject);
        }

        return $treeable;
    }

}
