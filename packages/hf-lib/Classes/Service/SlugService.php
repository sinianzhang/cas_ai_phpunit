<?php

namespace Hausformat\Lib\Service;

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

use Doctrine\DBAL\Types\Types;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\DataHandling\Model\RecordStateFactory;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

/**
 * Service class for generating and managing slugs for database records in TYPO3.
 * Provides methods to generate slugs based on table names, record UIDs, and TCA configurations, ensuring uniqueness in various contexts (site, PID, table).
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class SlugService
{

    /**
     * @var DataMapper
     */
    protected DataMapper $dataMapper;

    /**
     * @param AbstractEntity $abstractEntity
     *
     * @return string
     */
    public function generateSlug(AbstractEntity $abstractEntity)
    {

        $tablename = $this->dataMapper->convertClassNameToTableName(get_class($abstractEntity));
        $uid = $abstractEntity->getUid();

        return $this->generateSlugByTableNameAndUid($tablename, $uid);
    }


    /**
     * @param string $tablename
     * @param int $uid
     * @return string|null
     * @throws \TYPO3\CMS\Core\Exception\SiteNotFoundException
     */
    public function generateSlugByTableNameAndUid(string $tablename, int $uid)
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($tablename);

        $record = BackendUtility::getRecord($tablename, $uid);

        $slugField = $this->getSlugField($tablename);

        if (!$slugField) {
            return null;
        }

        $fieldName = $slugField['fieldName'];

        // Check for existing slugs from realurl
        $fieldConfig = $slugField['config'];
        $evalInfo = !empty($fieldConfig['eval']) ? GeneralUtility::trimExplode(',', $fieldConfig['eval'], true) : [];
        $hasToBeUnique = in_array('unique', $evalInfo, true);
        $hasToBeUniqueInSite = in_array('uniqueInSite', $evalInfo, true);
        $hasToBeUniqueInPid = in_array('uniqueInPid', $evalInfo, true);
        $slugHelper = GeneralUtility::makeInstance(SlugHelper::class, $tablename, $fieldName, $fieldConfig);

        $recordId = (int)$record['uid'];
        $pid = (int)$record['pid'];
        $slug = '';
        if ($pid === -1) {
            $queryBuilder = $connection->createQueryBuilder();
            $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
            $liveVersion = $queryBuilder
                ->select('pid')
                ->from($tablename)
                ->where(
                    $queryBuilder->expr()->eq('uid',
                        $queryBuilder->createNamedParameter($record['t3ver_oid'], Connection::PARAM_INT))
                )->executeQuery()->fetchAssociative();
            $pid = isset($liveVersion['pid']) ? (int) $liveVersion['pid'] : -1;
        }
        $slug = $slugHelper->generate($record, $pid);
        $state = RecordStateFactory::forName($tablename)
             ->fromArray($record, $pid, $recordId);
        if($hasToBeUnique && method_exists($slugHelper, 'isUniqueInTable') && !$slugHelper->isUniqueInTable($slug, $state)) {
            $slug = $slugHelper->buildSlugForUniqueInTable($slug, $state);
        }
        try {
            if ($hasToBeUniqueInSite && !$slugHelper->isUniqueInSite($slug, $state)) {
                $slug = $slugHelper->buildSlugForUniqueInSite($slug, $state);
            }
        } catch (\TYPO3\CMS\Core\Exception\SiteNotFoundException $e) {
            return null;
        }

        if ($hasToBeUniqueInPid && !$slugHelper->isUniqueInPid($slug, $state)) {
            $slug = $slugHelper->buildSlugForUniqueInPid($slug, $state);
        }


        return $slug;
    }

    /**
     * @param string $tableName
     *
     * @return array|null
     */
    protected function getSlugField(string $tableName)
    {

        $coulmnsConfig = $GLOBALS['TCA'][$tableName]['columns'];
        foreach ($coulmnsConfig as $fieldName => $fieldConfig) {
            if ($fieldConfig['config']['type'] === 'slug') {
                return [
                    'fieldName' => $fieldName,
                    'config' => $fieldConfig['config'],
                ];

            }
        }

        return null;
    }

    /**
     * @param DataMapper $dataMapper
     */
    public function injectDataMapper(DataMapper $dataMapper)
    {
        $this->dataMapper = $dataMapper;
    }

}
