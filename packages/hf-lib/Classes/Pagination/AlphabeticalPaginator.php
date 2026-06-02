<?php

declare(strict_types=1);

namespace Hausformat\Lib\Pagination;


use TYPO3\CMS\Core\Pagination\AbstractPaginator;
use TYPO3\CMS\Core\Pagination\PaginatorInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 *  Paginator class for paginating QueryResults alphabetically based on a specific field, typically used for titles.
 *  Supports pagination by letters (A-Z) and numerals (0-9), with optional inclusion of an "All" option.
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
final class AlphabeticalPaginator extends AbstractPaginator
{

    /**
     * @var QueryResultInterface
     */
    private $queryResult;

    /**
     * @var QueryResultInterface
     */
    private $paginatedQueryResult;

    /**
     * @var int
     */
    private $currentPageNumber = 0;

    protected $variableName = 'title';

    /**
     * @var bool
     */
    protected $addAll = false;

    /**
     * the values that can be used in the pagination
     *
     * @var array
     */
    protected $paginationValues = [
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        'P',
        'Q',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z',
        '0-9',
    ];

    public function __construct(QueryResultInterface $queryResult, $addAll = false, int $currentPage = 1, string $variableName = 'title')
    {
        $this->queryResult = $queryResult;
        $this->variableName = $variableName;
        $this->currentPageNumber = $currentPage -1;
        $this->addAll = $addAll;

        $this->updatePaginatedItems(0, 0);
    }

    public function withItemsPerPage(int $itemsPerPage): PaginatorInterface
    {
        throw new \BadMethodCallException('This Method should not be called in this Paginator', 1634902693);
    }


    public function withCurrentPageNumber(int $currentPageNumber): PaginatorInterface
    {
        if($currentPageNumber > 0) {
            $this->currentPageNumber = $currentPageNumber -1;
        }

        if ($currentPageNumber === $this->currentPageNumber) {
            return $this;
        }

        $new = clone $this;
        $new->setCurrentPageNumber($currentPageNumber);
        $new->updateInternalState();

        return $new;
    }


    public function getPaginatedItems(): iterable
    {
        return $this->paginatedQueryResult;
    }


    /**
     * @param int $itemsPerPage
     * @param int $offset
     * @return void
     * @throws InvalidQueryException
     */
    protected function updatePaginatedItems(int $itemsPerPage, int $offset): void
    {
        $actPage = $this->currentPageNumber;
        if($this->addAll && $actPage < 0) {
            $this->paginatedQueryResult = clone $this->queryResult;
            return;
        }

        $query = $this->queryResult->getQuery();
        $letter = $this->paginationValues[$actPage];
        $constraint = $this->getConstraintsForValue($query, $letter);
        $oldConstraints = $query->getConstraint();

        $this->paginatedQueryResult = $query->matching($query->logicalAnd($query->logicalOr(...$constraint), $oldConstraints))->execute();

    }

    protected function getTotalAmountOfItems(): int
    {
        return $this->queryResult->count();
    }

    /**
     * @return int
     */
    public function getTotalItemsCount(): int
    {
        return $this->getTotalAmountOfItems();
    }

    protected function getAmountOfItemsOnCurrentPage(): int
    {
        return $this->paginatedQueryResult->count();
    }

    /**
     * @return int
     */
    public function getCurrentPageItemsCount() {
        return $this->getAmountOfItemsOnCurrentPage();
    }

    protected function setCurrentPageNumber(int $currentPageNumber): void
    {
        $this->currentPageNumber = $currentPageNumber;
        $this->updatePaginatedItems(0, 0);
    }

    /**
     * @return array|string[]
     */
    public function getPaginationValues() {
        $values = $this->paginationValues;
        if($this->addAll) {
            array_unshift($values, 'addAll');
        }
        return $values;
    }


    public function getResultsPerPage() {
        $resultsByValue = [];
        foreach ($this->paginationValues as $value) {
            $count = $this->countResults($value);
            $resultsByValue[$value] = $count;
        }
        return $resultsByValue;
    }


    /**
     * @param $value
     * @return int
     * @throws InvalidQueryException
     */
    protected function countResults($value)
    {
        $query = $this->queryResult->getQuery();

        $originalConstraints = $query->getConstraint();

        $constraints = $this->getConstraintsForValue($query, $value);
        $query->matching( $query->logicalAnd($originalConstraints, ...$constraints));


        return $query->execute()->count();
    }


    /**
     * @param QueryInterface $query
     * @param $value
     * @return ConstraintInterface[]
     * @throws InvalidQueryException
     */
    protected function getConstraintsForValue(QueryInterface $query, $value): array
    {
        $allConstraints = [];

        if($value == '0-9') {
            for ($i = 0; $i < 10; $i++) {
                $constraint[] = $query->like($this->variableName, $i . '%');
            }
        } else {
            $constraint[] = $query->like($this->variableName, strtolower($value) . '%');
            $constraint[] = $query->like($this->variableName, $value . '%');
        }

        $allConstraints[] = $query->logicalOr(...$constraint);

        return $allConstraints;
    }


}
