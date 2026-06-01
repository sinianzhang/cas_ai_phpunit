<?php

declare(strict_types=1);

namespace B13\SiteT3demo\Backend\ToolbarItems;

/*
 * This file is part of TYPO3 CMS-extension site_t3demo by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use B13\SiteT3demo\RegistryInterface;
use TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;

final readonly class TimerToolbarItem implements ToolbarItemInterface
{
    public function __construct(
        PageRenderer $pageRenderer,
        private Registry $registry,
        private ViewFactoryInterface $viewFactory,
    ) {
        $pageRenderer->loadJavaScriptModule('@b13/site-t3demo/timer.js');
    }

    public function checkAccess(): bool
    {
        return true;
    }

    public function getItem(): string
    {
        if ((string)Environment::getContext() === 'Production') {
            $nextSync = $this->registry->get(RegistryInterface::NAMESPACE, RegistryInterface::KEY);
            $viewFactoryData = new ViewFactoryData(
                templateRootPaths: ['EXT:site_t3demo/Resources/Private/Serverreset/Templates/'],
            );
            $view = $this->viewFactory->create($viewFactoryData);
            $view->assign('nextSync', $nextSync);
            return $view->render('ToolbarItems/Timer');
        }
        return '';
    }

    public function hasDropDown(): bool
    {
        return false;
    }

    public function getDropDown(): string
    {
        return '';
    }

    public function getAdditionalAttributes(): array
    {
        return [];
    }

    public function getIndex(): int
    {
        return 10;
    }
}
