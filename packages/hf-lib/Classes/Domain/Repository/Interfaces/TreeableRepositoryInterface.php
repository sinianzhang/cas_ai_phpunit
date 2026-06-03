<?php


namespace Hausformat\Lib\Domain\Repository\Interfaces;


use Hausformat\Lib\Domain\Model\Interfaces\TreeableInterface;

/**
 * Repository interface for managing tree-structured models, which must implement the TreeableInterface.
 * Provides methods to retrieve objects by their parent node or to find root objects in the hierarchy.
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
interface TreeableRepositoryInterface
{

    /**
     * @param int|string|TreeableInterface $parent
     * @param bool $returnRawQuery
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAllByParent($parent, $returnRawQuery = false);

    /**
     * @param bool $returnRawQuery
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAllRootObjects($returnRawQuery = false);

}
