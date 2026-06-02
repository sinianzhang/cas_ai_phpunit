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
 * Interface for CacheTageServices
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
interface CacheTagServiceInterface
{

    /**
     * Adds a cache tag for the given class name.
     *
     * @param string $className
     *
     * @return mixed
     *
     * @api
     */
    public function addCacheTagForClassName(string $className);

    /**
     * Adds cache tags for a given domain object. (tablename_uid, tablename)
     * These cache tags are automatically cleared by the backend when a record is saved.
     *
     * @param \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface $domainObject
     *
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     *
     * @api
     */
    public function addCacheTagForDomainObject(DomainObjectInterface $domainObject);

    public function addCacheTag(string $domainObject);
}
