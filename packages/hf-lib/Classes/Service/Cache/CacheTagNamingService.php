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
 * Service class responsible for generating cache tags based on class names and domain objects.
 * The cache tags are derived from the corresponding database table names and object UIDs.
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class CacheTagNamingService
{
    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper
     *
     */
    protected $dataMapper;

    /**
     * @param string $className
     *
     * @return string
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     *
     * @api
     */
    public function getCacheTagForClass(string $className): string
    {
        return $this->dataMapper->getDataMap($className)->getTableName();
    }

    /**
     * @param DomainObjectInterface $domainObject
     *
     * @return string
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     *
     * @api
     */
    public function getCacheTagForDomainObject(DomainObjectInterface $domainObject): string
    {
        $className = get_class($domainObject);
        $tableName = $this->dataMapper->getDataMap($className)->getTableName();

        return $tableName . '_' . $domainObject->getUid();
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper $dataMapper
     */
    public function injectDataMapper(\TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper $dataMapper)
    {
        $this->dataMapper = $dataMapper;
    }
}
