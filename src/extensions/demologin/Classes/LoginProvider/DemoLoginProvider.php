<?php

declare(strict_types=1);

namespace B13\DemoLogin\LoginProvider;

/*
 * This file is part of TYPO3 CMS-based extension "demologin" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Backend\LoginProvider\LoginProviderInterface;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Fluid\View\FluidViewAdapter;

#[Autoconfigure(public: true)]
final readonly class DemoLoginProvider implements LoginProviderInterface
{
    private const array ALLOWED_USER_GROUPS = [2, 3];

    public function __construct(
        private ConnectionPool $connectionPool,
    ) {}

    public function modifyView(ServerRequestInterface $request, ViewInterface $view): string
    {
        if (!$view instanceof FluidViewAdapter) {
            return '';
        }
        $renderingContext = $view->getRenderingContext();
        $renderingContext->getTemplatePaths()->setTemplateRootPaths(['EXT:demologin/Resources/Private/Templates']);
        $view->assign('userGroups', $this->getPossibleUserGroups());
        return 'DemoLogin';
    }

    protected function getPossibleUserGroups(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('be_groups');
        return $queryBuilder
            ->select('*')
            ->from('be_groups')
            ->where(
                $queryBuilder->expr()->in('uid', $queryBuilder->createNamedParameter(self::ALLOWED_USER_GROUPS, Connection::PARAM_INT_ARRAY))
            )
            ->orderBy('uid', QueryInterface::ORDER_DESCENDING)
            ->executeQuery()
            ->fetchAllAssociative();
    }
}
