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

use Hausformat\Lib\Service\GlobalsService;
use TYPO3\CMS\Core\Cache\CacheTag;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;

/**
 * Service for adding CacheTags from a given Object
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class ObjectCacheTagService extends AbstractCacheTagService implements SingletonInterface
{
    /**
     * Remember what cache tags were already added so we don't add them more than once.
     *
     * @var array
     */
    protected $addedCacheTags = [];

    /**
     * @var \Hausformat\Lib\Service\GlobalsService
     */
    protected $globalsService;

    /**
     * @param \Hausformat\Lib\Service\GlobalsService $globalsService
     */
    public function injectGlobalsService(GlobalsService $globalsService) {
        $this->globalsService = $globalsService;
    }

    /**
     * @param DomainObjectInterface $object
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function addCacheTagForObject(DomainObjectInterface $object) {
        $this->addCacheTagForDomainObject($object);
    }

    /**
     * Adds a cache tag for the current request.
     *
     * @param string $cacheTag
     *
     * @api
     */
    public function addCacheTag(string $cacheTag): void
    {
        if (isset($this->addedCacheTags[$cacheTag])) {
            return;
        }

        $this->addedCacheTags[$cacheTag] = true;

        $cdc = $this->globalsService->getCacheDataCollector();
        $cdc->addCacheTags(new CacheTag($cacheTag));
    }
}
