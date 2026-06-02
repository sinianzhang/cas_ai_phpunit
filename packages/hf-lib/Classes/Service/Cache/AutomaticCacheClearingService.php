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
 * Service class responsible for automatically clearing cache entries when domain objects are updated.
 * Utilizes the CacheTagNamingService to generate appropriate cache tags and clears caches or cache groups accordingly.
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class AutomaticCacheClearingService
{
    /**
     * @var \TYPO3\CMS\Core\Cache\CacheManager
     *
     */
    protected $cacheManager;

    /**
     * @var \Hausformat\Lib\Service\Cache\CacheTagNamingService
     *
     */
    protected $cacheTagNamingService;

    /**
     * @param \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface $object
     *
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheGroupException
     *
     * @internal This method is registered as hook. Do not use directly.
     */
    public function afterUpdateObject(DomainObjectInterface $object)
    {
        $this->clearCacheForObject($object);
    }

    /**
     * @param \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface $object
     *
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheGroupException
     */
    protected function clearCacheForObject(DomainObjectInterface $object)
    {
        $className = get_class($object);

        if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['hf_lib']['automaticCacheClearing'][$className])) {
            return;
        }

        $classConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['hf_lib']['automaticCacheClearing'][$className];

        $cacheTags = [
            $this->cacheTagNamingService->getCacheTagForClass($className),
            $this->cacheTagNamingService->getCacheTagForDomainObject($object),
        ];

        if (isset($classConfiguration['caches'])) {
            foreach ($classConfiguration['caches'] as $cacheName) {
                $this->cacheManager->getCache($cacheName)->flushByTags($cacheTags);
            }
        }

        if (isset($classConfiguration['cacheGroups'])) {
            foreach ($classConfiguration['cacheGroups'] as $groupName) {
                $this->cacheManager->flushCachesInGroupByTags($groupName, $cacheTags);
            }
        }

        if (isset($classConfiguration['followProperties'])) {
            foreach ($classConfiguration['followProperties'] as $propertyName) {
                $propertyValue = $object->_getProperty($propertyName);
                $cleanPropertyValue = $object->_getCleanProperty($propertyName);

                if ($propertyValue instanceof DomainObjectInterface) {
                    $this->clearCacheForObject($propertyValue);
                }

                if ($cleanPropertyValue instanceof DomainObjectInterface) {
                    $this->clearCacheForObject($cleanPropertyValue);
                }
            }
        }
    }

    /**
     * @param \TYPO3\CMS\Core\Cache\CacheManager $cacheManager
     */
    public function injectCacheManager(\TYPO3\CMS\Core\Cache\CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * @param \Hausformat\Lib\Service\Cache\CacheTagNamingService $cacheTagNamingService
     */
    public function injectCacheTagNamingService(\Hausformat\Lib\Service\Cache\CacheTagNamingService $cacheTagNamingService)
    {
        $this->cacheTagNamingService = $cacheTagNamingService;
    }
}
