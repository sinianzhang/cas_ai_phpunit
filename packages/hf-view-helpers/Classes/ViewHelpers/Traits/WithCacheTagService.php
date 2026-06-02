<?php

namespace Hausformat\ViewHelpers\ViewHelpers\Traits;

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

use Hausformat\Lib\Service\Cache\CacheTagServiceInterface;
use Hausformat\Lib\Service\Cache\PagesCacheTagService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * @author    .hausformat Development Team
 * @copyright 2018 .hausformat GmbH
 */
trait WithCacheTagService
{
    /**
     * @param RenderingContextInterface $renderingContext
     *
     * @return CacheTagServiceInterface|object
     */
    protected static function getCacheTagService(RenderingContextInterface $renderingContext)
    {
        $variableContainer = $renderingContext->getViewHelperVariableContainer();

        if ($variableContainer->exists(self::class, self::getVariableName())) {
            return $variableContainer->get(self::class, self::getVariableName());
        }

        return GeneralUtility::makeInstance(PagesCacheTagService::class);
    }

    /**
     * @return string
     */
    private static function getVariableName()
    {
        return 'cacheTagService';
    }

    /**
     * @param \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
     */
    protected static function removeCacheTagService(RenderingContextInterface $renderingContext)
    {
        $renderingContext->getViewHelperVariableContainer()->remove(self::class, self::getVariableName());
    }

    /**
     * @param RenderingContextInterface $renderingContext
     * @param CacheTagServiceInterface $cacheTagService
     */
    protected static function setCacheTagService(
        RenderingContextInterface $renderingContext,
        CacheTagServiceInterface $cacheTagService
    )
    {
        $renderingContext->getViewHelperVariableContainer()->add(self::class, self::getVariableName(), $cacheTagService);
    }
}
