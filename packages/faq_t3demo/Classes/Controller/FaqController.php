<?php

declare(strict_types=1);

namespace B13\FaqT3demo\Controller;

/*
 * This file is part of TYPO3 CMS-based extension "faq_t3demo" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use B13\FaqT3demo\RecordList\DatabaseRecordList;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Module\ModuleData;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[AsController]
final readonly class FaqController
{
    private const RECORDS_TO_DISPLAY = 100;

    public function __construct(
        private PageRenderer $pageRenderer,
        private FlashMessageService $flashMessageService,
        private ModuleTemplateFactory $moduleTemplateFactory,
        #[Autowire(expression: 'service("extension-configuration").get("faq_t3demo", "pid")')]
        private int $faqPid,
    ) {}

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        $filter = $request->getQueryParams()['filter'] ?? $request->getParsedBody()['filter'] ?? '';
        $dblist = $this->getDatabaseRecordList($filter, $request);
        if ($dblist->getTotalItems('tx_faqt3demo_faq', $this->faqPid) === 0) {
            $flashMessageQueue = $this->flashMessageService->getMessageQueueByIdentifier();
            if ($filter === '') {
                $flashMessageQueue->enqueue(
                    new FlashMessage(
                        $this->getLanguageService()->sL('LLL:EXT:faq_t3demo/Resources/Private/Language/locallang_module.xlf:flashMessage.no_records.created'),
                        $this->getLanguageService()->sL('LLL:EXT:faq_t3demo/Resources/Private/Language/locallang_module.xlf:flashMessage.no_records.title'),
                        ContextualFeedbackSeverity::INFO
                    )
                );
            } else {
                $flashMessageQueue->enqueue(
                    new FlashMessage(
                        sprintf($this->getLanguageService()->sL('LLL:EXT:faq_t3demo/Resources/Private/Language/locallang_module.xlf:flashMessage.no_records.found'), $filter),
                        $this->getLanguageService()->sL('LLL:EXT:faq_t3demo/Resources/Private/Language/locallang_module.xlf:flashMessage.no_records.title'),
                        ContextualFeedbackSeverity::INFO
                    )
                );
            }
        }

        $this->pageRenderer->addInlineLanguageLabelFile('EXT:core/Resources/Private/Language/locallang_mod_web_list.xlf');
        $this->pageRenderer->loadJavaScriptModule('@typo3/backend/recordlist.js');
        $this->pageRenderer->loadJavaScriptModule('@typo3/backend/action-dispatcher.js');
        $this->pageRenderer->loadJavaScriptModule('@typo3/backend/context-menu.js');

        $view = $this->moduleTemplateFactory->create($request);
        $view->assign('dblist', $dblist->generateList());
        $view->assign('filter', $filter);
        $view->assign('faqPid', $this->faqPid);

        return $view->renderResponse('Faq/List');
    }

    private function getDatabaseRecordList(string $filter, ServerRequestInterface $request): DatabaseRecordList
    {
        $pageinfo = BackendUtility::readPageAccess($this->faqPid, $this->getBackendUser()->getPagePermsClause(Permission::PAGE_SHOW));
        $dblist = GeneralUtility::makeInstance(DatabaseRecordList::class);
        $dblist->pageRow = $pageinfo;
        $dblist->calcPerms = new Permission($this->getBackendUser()->calcPerms($pageinfo));
        $dblist->displayColumnSelector = false;
        $dblist->displayRecordDownload = false;
        $dblist->setRequest($request);
        $dblist->start($this->faqPid, 'tx_faqt3demo_faq', (int)($request->getQueryParams()['pointer'] ?? 0), $filter, 0, self::RECORDS_TO_DISPLAY);
        $moduleData = new ModuleData('tx_faqt3demo_faq', ['bigControlPanel' => true]);
        $dblist->setModuleData($moduleData);
        return $dblist;
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
