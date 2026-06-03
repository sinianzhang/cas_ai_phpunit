<?php declare(strict_types=1);

namespace Hausformat\ViewHelpers\ViewHelpers\Debug;

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

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Debug Cache Tags
 * 
 * @author .hausformat <entwicklung@hausformat.com>
 */
class CacheTagsViewHelper extends AbstractViewHelper
{
    /**
     * @return void
     * @throws \ReflectionException
     */
    public function render(): void
    {
        $request = $this->renderingContext->getAttribute(ServerRequestInterface::class);
        $cacheCollector = $request->getAttribute('frontend.cache.collector');
        $pageCacheTags = $cacheCollector->getCacheTags();
        $pageCacheTags->setAccessible(true);

        DebuggerUtility::var_dump($pageCacheTags);
    }
}
