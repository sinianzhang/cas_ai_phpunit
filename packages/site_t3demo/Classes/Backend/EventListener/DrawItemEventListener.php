<?php

declare(strict_types=1);

namespace B13\SiteT3demo\Backend\EventListener;

/*
 * This file is part of TYPO3 CMS-extension site_t3demo by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Doctrine\DBAL\ArrayParameterType;
use TYPO3\CMS\Backend\View\Event\PageContentPreviewRenderingEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Domain\Record;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final readonly class DrawItemEventListener
{
    public function __construct(
        private ConnectionPool $connectionPool,
    ) {}

    #[AsEventListener(identifier: 'site-t3demo/preview-rendering-ctype')]
    public function __invoke(PageContentPreviewRenderingEvent $event): void
    {
        if ($event->getTable() !== 'tt_content'
            || $event->getRecord()->getRecordType() !== 'menu_subpages'
        ) {
            return;
        }

        // Add list of 'sub pages' for CType 'menu_subpages' to record. Used in Menu_subpages.fluid.html CE preview.
        // @todo: Should probably be turned into a VH call in Menu_subpages template or similar to be more straight forward.
        /** @var Record $recordBefore */
        $recordBefore = $event->getRecord();
        $recordBeforeRaw = $recordBefore->toArray();
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('pages');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $subPages = $queryBuilder
            ->select('*')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->in(
                    'pid',
                    $queryBuilder->createNamedParameter(explode(',', ((string)$recordBeforeRaw['pages'] ?: (string)$recordBefore->getPid())), ArrayParameterType::INTEGER)
                ),
                $queryBuilder->expr()->eq(
                    'sys_language_uid',
                    $queryBuilder->createNamedParameter($recordBefore->getRawRecord()->get('sys_language_uid'))
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
        $recordProperties = $recordBefore->toArray(false);
        $recordProperties['subPages'] = $subPages;
        $newRecord = new Record($recordBefore->getRawRecord(), $recordProperties, $recordBefore->getSystemProperties());
        $event->setRecord($newRecord);
    }
}
