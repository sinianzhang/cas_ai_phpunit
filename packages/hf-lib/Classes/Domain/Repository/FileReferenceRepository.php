<?php

namespace Hausformat\Lib\Domain\Repository;

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

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * The default Repository for \Hausformat\Lib\Domain\Model\FileReferences
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class FileReferenceRepository extends \Hausformat\Lib\Domain\Repository\BaseRepository
{

    /**
     * Returns all objects of this repository.
     *
     * @param int|string $uidForeign
     * @param string $tablename
     * @param bool $getRawQuery
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array
     * @api
     */
    public function findAllByUidForeign($uidForeign, $tablename = '', $getRawQuery = false)
    {
        $query = $this->createQueryWithoutTS();

        $frameworkConfiguration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $persistence = $frameworkConfiguration['persistence'];
        if (isset($persistence[$this->objectType])) {
            $querySettings = $query->getQuerySettings();
            $this->setQuerySettingsFromPersistenceTS($querySettings);
        }

        $constraints[] = $query->equals('uidForeign', $uidForeign);
        if ($tablename != '') {
            $constraints[] = $query->equals('tablenames', $tablename);
        }

        $query->matching($query->logicalAnd(...$constraints));

        return $query->execute($getRawQuery);
    }

    /**
     * Returns all objects of this repository.
     *
     * @param int|string $uidForeign
     * @param string $tablename
     * @param string $fieldname
     * @param bool $getRawQuery
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array
     * @api
     */
    public function findAllByUidForeignAndFieldname($uidForeign, $tablename, $fieldname, $getRawQuery = false)
    {
        $query = $this->createQueryWithoutTS();

        $frameworkConfiguration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $persistence = $frameworkConfiguration['persistence'];
        if (isset($persistence[$this->objectType])) {
            $querySettings = $query->getQuerySettings();
            $this->setQuerySettingsFromPersistenceTS($querySettings);
        }

        $constraints[] = $query->equals('uidForeign', $uidForeign);
        $constraints[] = $query->equals('tablenames', $tablename);
        $constraints[] = $query->equals('fieldname', $fieldname);

        $query->matching($query->logicalAnd(...$constraints));
        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->execute($getRawQuery);
    }

    /**
     * @param int $uidForeign
     * @param string $tablename
     * @param string $fieldname
     *
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function removeAllByUidForeignAndFieldname(int $uidForeign, string $tablename, string $fieldname = '*')
    {
        $query = $this->createQueryWithoutTS();

        $constraints = [
            $query->equals('uidForeign', $uidForeign),
            $query->equals('tablenames', $tablename),
        ];

        if ($fieldname !== '*') {
            $constraints[] = $query->equals('fieldname', $fieldname);
        }

        $query->matching($query->logicalAnd(...$constraints));
        $query->getQuerySettings()->setRespectStoragePage(false);

        foreach ($query->execute() as $reference) {
            $this->remove($reference);
        }
    }
}
