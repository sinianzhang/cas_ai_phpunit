<?php

namespace B13\SiteT3demo\ViewHelpers;

/*
 This file is part of TYPO3 CMS-extension site_t3demo by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to get the rootline of a page.
 */
class RootlineViewHelper extends AbstractViewHelper
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * RootlineViewHelper constructor.
     * @param Context|null $context
     * @param PageRepository|null $pageRepository
     */
    public function __construct(Context $context = null, PageRepository $pageRepository = null)
    {
        $this->context = $context ?? GeneralUtility::makeInstance(Context::class);
        $this->pageRepository = $pageRepository ?? GeneralUtility::makeInstance(PageRepository::class, $this->context);
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('pageUid', 'integer', 'page uid to use.', true);
        $this->registerArgument('skipGivenPage', 'bool', 'if set the given page is excluded from the root line', false);
    }

    /**
     * @return array
     */
    public function render(): array
    {
        $pageUid = (int)$this->arguments['pageUid'];
        $skipCurrentPage = (bool)$this->arguments['skipGivenPage'] ?: false;
        $pages = [];
        try {
            // throws an exception if one parent page ist deleted
            $rootlineUtility = GeneralUtility::makeInstance(RootlineUtility::class, $pageUid);
            $rootline = $rootlineUtility->get();
            foreach ($rootline as $pageInRootLine) {
                if ($skipCurrentPage && (int)$pageInRootLine['uid'] == $pageUid) {
                    continue;
                }
                $page = $this->pageRepository->getPage((int)$pageInRootLine['uid']);
                if (!isset($page['uid'])) {
                    continue;
                }
                $pages[] = $page;
            }
        } catch (\Exception $e) {
        }
        return $pages;
    }
}
