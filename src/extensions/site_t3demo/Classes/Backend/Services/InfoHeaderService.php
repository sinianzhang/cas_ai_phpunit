<?php

namespace B13\SiteT3demo\Backend\Services;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;

final readonly class InfoHeaderService
{
    public function __construct(
        private ViewFactoryInterface $viewFactory,
    ) {}

    public function getInfo(ServerRequestInterface $request): string
    {
        $pageUid = (int)($request->getParsedBody()['id'] ?? $request->getQueryParams()['id'] ?? 0);
        $pageInfo = BackendUtility::readPageAccess($pageUid, $GLOBALS['BE_USER']->getPagePermsClause(Permission::PAGE_SHOW));
        // Nothing if this page does not have any notes of interest, and if there is no header we do not add anything
        $content = '';
        if (!empty($pageInfo['infotext_header'])) {
            $viewFactoryData = new ViewFactoryData(
                templateRootPaths: ['EXT:site_t3demo/Resources/Private/Backend/Templates/'],
                request: $request,
            );
            $view = $this->viewFactory->create($viewFactoryData);
            $view->assignMultiple([
                'data' => $pageInfo,
                'pageUid' => $pageUid,
            ]);
            $content = $view->render('PageLayout/Header');
        }
        return $content;
    }
}
