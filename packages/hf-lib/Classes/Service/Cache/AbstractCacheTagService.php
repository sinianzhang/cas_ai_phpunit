<?php
declare(strict_types=1);

namespace Hausformat\Lib\Service\Cache;

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

use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;

/**
 * Abstract class providing common functionality for cache tag management, using the CacheTagNamingService to generate cache tags.
 * Implements methods for adding cache tags based on class names or domain objects.
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
abstract class AbstractCacheTagService implements CacheTagServiceInterface
{
    /**
     * @var \Hausformat\Lib\Service\Cache\CacheTagNamingService
     */
    protected $cacheTagNamingService = null;

    /**
     * @param \Hausformat\Lib\Service\Cache\CacheTagNamingService $cacheTagNamingService
     */
    public function injectCacheTagNamingService(CacheTagNamingService $cacheTagNamingService)
    {
        $this->cacheTagNamingService = $cacheTagNamingService;
    }

    /**
     * @inheritdoc
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function addCacheTagForClassName(string $className)
    {
        $this->addCacheTag($this->cacheTagNamingService->getCacheTagForClass($className));
    }

    /**
     * @inheritdoc
     */
    public function addCacheTagForDomainObject(DomainObjectInterface $domainObject)
    {
        $this->addCacheTag($this->cacheTagNamingService->getCacheTagForDomainObject($domainObject));
    }
}
