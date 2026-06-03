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
use TYPO3\CMS\Backend\Controller\Event\RenderAdditionalContentToRecordListEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;

final readonly class RecordListHeaderEventListener
{
    public function __construct(
        private InfoHeaderService $infoHeaderService,
    ) {}

    #[AsEventListener(identifier: 'site-t3demo/backend/recordlist-add-infotext-header')]
    public function __invoke(RenderAdditionalContentToRecordListEvent $event): void
    {
        $event->addContentAbove($this->infoHeaderService->getInfo($event->getRequest()));
    }
}
