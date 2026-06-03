<?php

declare(strict_types=1);

namespace B13\FaqT3demo\RecordList;

/*
 * This file is part of TYPO3 CMS-based extension "faq_t3demo" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(public: true)]
class DatabaseRecordList extends \TYPO3\CMS\Backend\RecordList\DatabaseRecordList
{
    /**
     * Rendering the header row for a table
     *
     * @param string $table Table name
     * @param int[] $currentIdList Array of the currently displayed uids of the table
     * @throws \UnexpectedValueException
     * @return string Header table row
     * @internal
     * @see getTable()
     */
    public function renderListHeader(string $table, array $currentIdList): string
    {
        // empty head
        return '<thead></thead>';
    }

    /**
     * Get total items of RecordList
     *
     * @param string $table
     * @param int $id
     * @return int
     */
    public function getTotalItems(string $table, int $id): int
    {
        $queryBuilderTotalItems = $this->getQueryBuilder($table, ['*'], false, 0, 1);
        return (int)$queryBuilderTotalItems->count('*')
            ->executeQuery()
            ->fetchOne();
    }
}
