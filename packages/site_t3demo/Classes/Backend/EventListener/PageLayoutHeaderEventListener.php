<?php

declare(strict_types=1);

/*
 * This file is part of the package site_t3demo by b13.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace B13\SiteT3demo\Backend\EventListener;

use B13\SiteT3demo\Backend\Services\InfoHeaderService;
use TYPO3\CMS\Backend\Controller\Event\ModifyPageLayoutContentEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Page\PageRenderer;

final readonly class PageLayoutHeaderEventListener
{
    public function __construct(
        private InfoHeaderService $infoHeaderService,
        private PageRenderer $pageRenderer,
    ) {}

    #[AsEventListener(identifier: 'site-t3demo/backend/pagelayout-add-infotext-header')]
    public function __invoke(ModifyPageLayoutContentEvent $event): void
    {
        $this->pageRenderer->addCssFile('EXT:site_t3demo/Resources/Public/Backend/Css/Skin/t3skin_override.css');
        $event->addHeaderContent($this->infoHeaderService->getInfo($event->getRequest()));
    }
}
