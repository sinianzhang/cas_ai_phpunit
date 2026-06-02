<?php

namespace Hausformat\ViewHelpers\ViewHelpers\Cache;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Hausformat\ViewHelpers\ViewHelpers\Traits\WithCacheTagService;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Persistence\Generic\Exception;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * TODO: Document this
 * 
 * @group hf-viewhelpers
 * @author .hausformat <entwicklung@hausformat.com>
 */
class AddCacheTagViewHelper extends AbstractViewHelper
{
    use WithCacheTagService;

    /**
     * @throws Exception
     */
    public function render(): void
    {
        $for = $this->arguments['for'] ?? null;
        if ($for instanceof LazyLoadingProxy) {
            $for = $for->_loadRealInstance();
        }

        $renderingContext = $this->renderingContext;
        $cacheTagService = self::getCacheTagService($renderingContext);

        if ($for instanceof AbstractDomainObject) {
            $cacheTagService->addCacheTagForDomainObject($for);
        } else {
            $cacheTagService->addCacheTagForClassName((string)$for);
        }
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('for', 'mixed', '', false);
    }
}
