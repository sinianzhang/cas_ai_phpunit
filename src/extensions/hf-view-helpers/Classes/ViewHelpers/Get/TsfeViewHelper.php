<?php

namespace Hausformat\ViewHelpers\ViewHelpers\Get;

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

use Hausformat\Lib\Utility\ObjectStorageUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Get a value from the TSFE by key or the whole TSFE object
 *
 * ## Examples
 *
 *     <hf:get.tsfe key="id" /> // returns the current page uid
 *     <hf:get.tsfe /> // returns the whole TSFE object
 *
 * @author .hausformat <entwicklung@hausformat.com>
 * @deprecated for hausformat in TYPO3 14, to be removed in TYPO3 V15
 */
class TsfeViewHelper extends AbstractViewHelper
{
    /**
     * @return mixed|null
     */
    public function render(): mixed
    {
        $request = $this->renderingContext->getAttribute(ServerRequestInterface::class);
        $context = [
            'request' => $request,
            'pageInformation' => $request->getAttribute('frontend.page.information'),
            'page' => $request->getAttribute('frontend.page.information')?->getPageRecord(),
            'id' => $request->getAttribute('frontend.page.information')?->getId(),
            'rootLine' => $request->getAttribute('frontend.page.information')?->getRootLine(),
            'site' => $request->getAttribute('site'),
            'language' => $request->getAttribute('language'),
            'cacheTags' => $request->getAttribute('frontend.cache.collector')?->getCacheTags(),
        ];

        if (empty($this->arguments['key'])) {
            return $context;
        }

        return ObjectStorageUtility::getValueFromDottedPath($this->arguments['key'], $context);
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('key', 'string', 'Key to get from the TSFE');
    }
}
