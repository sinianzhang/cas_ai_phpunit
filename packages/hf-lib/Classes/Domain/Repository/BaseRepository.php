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

use Hausformat\Lib\Domain\Model\Interfaces\TranslatableInterface;
use Hausformat\Lib\Service\GlobalsService;
use Hausformat\Lib\Service\TypoScriptService;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * The base HF repository for Objects witch implements \Hausformat\Lib\Domain\Model\AbstractEntity.
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 *
 * @method array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface findBySorting(int $uid, bool $getRAWQuery = false)
 * @method array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface findOneBySorting(int $uid, bool $getRAWQuery = false)
 * @method int countBySorting(int $uid)
 * @method array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface findByPid(int $pid, bool $getRAWQuery = false)
 * @method array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface findOneByPid(int $pid, bool $getRAWQuery = false)
 * @method int countByPid(int $pid)
 * @method array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface findByLanguageUid(int $languageUid, bool $getRAWQuery = false)
 * @method array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface findOneByLanguageUid(int $languageUid, bool $getRAWQuery = false)
 * @method int countByLanguageUid(int $languageUid)
 * @method array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface findByHidden(int $hidden, bool $getRAWQuery = false)
 * @method array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface findOneByHidden(int $hidden, bool $getRAWQuery = false)
 * @method int countByHidden(int $hidden)
 */
abstract class BaseRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * ConfigurationManager is Used in Constructor, so no injection can be done.
     *
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
     */
    protected $configurationManager;

    /**
     * @var int
     */
    protected $defaultLimit;

    /**
     * @var int
     */
    protected $defaultOffset;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\Session
     *
     */
    protected $persistenceSession;

    /**
     * @var bool
     */
    protected $respectStoragePage;

    /**
     *
     * @var bool
     */
    protected $respectSysLanguage;

    /**
     * Contains the settings of the current extension
     *
     * @var array
     * @api
     */
    protected $settings;

    /**
     * Constructs a new Repository
     */
    public function __construct()
    {

        parent::__construct();

        $this->configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);

        $this->setOrderingFromTS();
        $this->initializeRespectSysLanguageFromTS();
        $this->initializeRespectStoragePageFromTS();

        if ($this->settings == null || $this->settings == []) {
            $this->settings = TypoScriptService::getInstance()->getFrameworkConfig('settings');
        }
    }

    /**
     * @param string $settingsName
     *
     * @return void
     */
    protected function setOrderingFromTS($settingsName = '')
    {
        $orderByString = '';
        if ($settingsName == '') {
            $settingsName = $this->objectType;
        }
        if($this->configurationManager == null) {
            return;
        }
        $frameworkConfiguration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);

        if (isset($frameworkConfiguration['persistence'][$settingsName]['defaultOrderBy'])) {
            $orderByString = $frameworkConfiguration['persistence'][$settingsName]['defaultOrderBy'];
        } elseif (isset($frameworkConfiguration['persistence']['defaultOrderBy'])) {
            $orderByString = $frameworkConfiguration['persistence']['defaultOrderBy'];
        }

        if ($orderByString != '') {
            $this->defaultOrderings = [];
            $allOrders = explode(',', $orderByString);
            foreach ($allOrders AS $oneOrderByClause) {
                $oneOrderByClause = trim($oneOrderByClause);

                $orderAscDescArray = explode(' ', $oneOrderByClause);
                $AscDesc = QueryInterface::ORDER_ASCENDING;
                if (isset($orderAscDescArray[1]) && strtolower($orderAscDescArray[1]) == strtolower(QueryInterface::ORDER_DESCENDING)) {
                    $AscDesc = QueryInterface::ORDER_DESCENDING;
                }

                $this->defaultOrderings[$orderAscDescArray[0]] = $AscDesc;
            }
        }

    }

    /**
     * @return void
     */
    protected function initializeRespectSysLanguageFromTS()
    {
        $persistence = $this->getPersistenceTS();

        if (isset($persistence[$this->objectType]['respectSysLanguage'])) {
            $this->respectSysLanguage = (bool)$persistence[$this->objectType]['respectSysLanguage'];
        } elseif (isset($persistence['respectSysLanguage'])) {
            $this->respectSysLanguage = (bool)$persistence['respectSysLanguage'];
        }
    }

    /**
     * @return mixed
     */
    protected function getPersistenceTS()
    {
        $frameworkConfiguration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        return $frameworkConfiguration['persistence'];
    }

    /**
     * @return void
     */
    protected function initializeRespectStoragePageFromTS()
    {
        $persistence = $this->getPersistenceTS();

        if (isset($persistence[$this->objectType]['respectStoragePage'])) {
            $this->respectStoragePage = (bool)$persistence[$this->objectType]['respectStoragePage'];
        } elseif (isset($persistence['respectStoragePage'])) {
            $this->respectStoragePage = (bool)$persistence['respectStoragePage'];
        }
    }

    /**
     *  Dispatches magic methods (findBy[Property]())
     *  Extends the magic method from the core to include the functionality that the query can be returned RAW,
     *  and gives the possibility to enable further comparisons in addition to only "equals"
     *
     * @param $methodName
     * @param $arguments
     *
     * @return int|mixed|mixed[]|\mixed[][]|object|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface|null
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception\UnsupportedMethodException
     */
    public function __call($methodName, $arguments)
    {
        $getRAWQuery = false;

        if (isset($arguments[1]) && $arguments[1]) {
            $getRAWQuery = true;
        }
        $matching = 'equals';
        if (isset($arguments[2]) && $arguments[2] && is_string($arguments[2])) {
            $matching = $arguments[2];
        }

        if (str_starts_with($methodName, 'findBy') && strlen($methodName) > 7) {
            $propertyName = lcfirst(substr($methodName, 6));
            $query = $this->createQueryWithOffsetAndLimit();
            if (!method_exists($query, $matching)) {
                $matching = 'equals';
            }
            return $query->matching($query->{$matching}($propertyName, $arguments[0]))->execute($getRAWQuery);
        }
        if (str_starts_with($methodName, 'findOneBy') && strlen($methodName) > 10) {
            $propertyName = lcfirst(substr($methodName, 9));
            $query = $this->createQueryWithOffsetAndLimit();
            if (!method_exists($query, $matching)) {
                $matching = 'equals';
            }
            $result = $query->matching($query->{$matching}($propertyName, $arguments[0]))->setLimit(1)->execute($getRAWQuery);
            if ($result instanceof QueryResultInterface) {
                return $result->getFirst();
            }
            if (is_array($result)) {
                return $result[0] ?? null;
            }
        } elseif (str_starts_with($methodName, 'countBy') && strlen($methodName) > 8) {
            if (isset($arguments[1]) && $arguments[1] && is_string($arguments[1])) {
                $matching = $arguments[2];
            }
            $propertyName = lcfirst(substr($methodName, 7));
            $query = $this->createQuery();
            if (!is_string($matching) || !method_exists($query, $matching)) {
                $matching = 'equals';
            }

            return count($query->matching($query->{$matching}($propertyName, $arguments[ 0]))->execute(true));
        }

        throw new \TYPO3\CMS\Extbase\Persistence\Generic\Exception\UnsupportedMethodException('The method "' . $methodName . '" is not supported by the repository.',
                                                                                              1233180480);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
     */
    public function createQueryWithOffsetAndLimit()
    {
        $query = parent::createQuery();
        try {
            $this->setGeneralQuerySettingsFromTS($query);
        } catch (\Exception $ignored) {
        }
        $this->addOffsetAndLimit($query);

        return $query;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\QueryInterface $query
     *
     */
    protected function setGeneralQuerySettingsFromTS(&$query)
    {
        $querySettings = $query->getQuerySettings();
        $this->setQuerySettingsFromPersistenceTS($querySettings);
        $query->setQuerySettings($querySettings);
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface $querySettings
     */
    protected function setQuerySettingsFromPersistenceTS(&$querySettings)
    {

        $persistence = $this->getPersistenceTS();

        if ($this->respectSysLanguage !== null && !$this->respectSysLanguage) {
            $querySettings->setRespectSysLanguage(false);
        }

        if ($this->respectStoragePage !== null && !$this->respectStoragePage) {
            $querySettings->setRespectStoragePage(false);
        }

        if (($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
            && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()) {
            if (!$this->respectSysLanguage) {
                $querySettings->setRespectSysLanguage(false);
            }

            if (!$this->respectStoragePage) {
                $querySettings->setRespectStoragePage(false);
            }
        }

        if ((($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
                && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend())
            || (isset($persistence[$this->objectType]['simulateBeEnvironment'])
                && $persistence[$this->objectType]['simulateBeEnvironment'] == 1)) {
            if (!isset($persistence[$this->objectType]['ignoreEnableFields']) || $persistence[$this->objectType]['ignoreEnableFields'] == 0) {
                $querySettings->setIgnoreEnableFields(true);
                // define single fields to be ignored
                if (isset($persistence[$this->objectType]['enabledFieldsToBeIgnored']) && $persistence[$this->objectType]['enabledFieldsToBeIgnored'] != '') {
                    $enabledFieldsToBeIgnored = GeneralUtility::trimExplode(',',
                                                                            $persistence[$this->objectType]['enabledFieldsToBeIgnored']);
                    $querySettings->setEnableFieldsToBeIgnored($enabledFieldsToBeIgnored);
                } else {
                    $querySettings->setEnableFieldsToBeIgnored(['disabled', 'starttime', 'endtime', 'hidden']);
                }
            }
        }

    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\QueryInterface $query
     */
    protected function addOffsetAndLimit(&$query)
    {
        if ($this->defaultLimit) {
            $query->setLimit($this->defaultLimit);
        }

        if ($this->defaultOffset) {
            $query->setOffset($this->defaultOffset);
        }
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
     */
    public function createQuery()
    {
        $query = parent::createQuery();
        try {
            $this->setGeneralQuerySettingsFromTS($query);
        } catch (\Exception $ignored) {
        }

        return $query;
    }


    /**
     * find multiple objectes by their uids
     *
     * @param array $uids an array with uids
     *
     * @return int
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function countAllByUid(?array $uids = null)
    {
        if (is_array($uids) && !empty($uids)) {
            $query = $this->createQuery();
            $query->matching($query->in('uid', $uids));

            return count($query->execute(true));
        } else {
            return 0;
        }
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
     */
    public function createQueryWithoutTS()
    {
        return parent::createQuery();
    }

    /**
     * Returns all objects of this repository.
     *
     * @param bool $getRawQuery
     *
     * @return QueryResultInterface|array
     * @api
     */
    public function findAll($getRawQuery = false)
    {
        return $this->createQueryWithOffsetAndLimit()->execute($getRawQuery);
    }

    /**
     * find all translated items for the passed l10nParent Uid
     *
     * @param      $l10nParent
     * @param bool $getRawQuery
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \Doctrine\DBAL\Exception
     */
    public function findAllByL10nParent($l10nParent, $getRawQuery = false)
    {
        $dataMapper = GeneralUtility::makeInstance(DataMapper::class);

        $table = $dataMapper->convertClassNameToTableName($this->objectType);

        $select = '*';

        $where = '(sys_language_uid NOT IN (0) AND l10n_parent = ' . $l10nParent . ') AND deleted=0 AND hidden=0 ';

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

        $returnObjects = $queryBuilder->select($select)
                                      ->from($table)
                                      ->where($where)
                                      ->executeQuery()
                                      ->fetchAllAssociative();

        if (!$getRawQuery && is_array($returnObjects)) {
            $returnObjects = $dataMapper->map($this->objectType, $returnObjects);
        }

        return $returnObjects;

    }

    /**
     * find multiple objectes by their uids
     *
     * @param array|NULL $uids
     * @param bool $getRAWQuery
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findAllByUid(?array $uids = null, $getRAWQuery = false)
    {
        if (is_array($uids) && !empty($uids)) {
            $query = $this->createQueryWithOffsetAndLimit();
            $query->matching($query->in('uid', $uids));

            return $query->execute($getRAWQuery);
        } else {
            return [];
        }
    }

    /**
     * find all translated items for the passed l10nParent Uid
     *
     * @param int|AbstractEntity $l10nParent
     * @param int $desiredLanguage
     * @param bool $getRawQuery
     *
     * @return array|\Hausformat\Lib\Domain\Model\AbstractEntity|AbstractEntity|null
     */
    public function findByL10nParentAndLanguage($l10nParent, $desiredLanguage, $getRawQuery = false)
    {
        $uid = $l10nParent;
        if ($l10nParent instanceof AbstractEntity) {
            $uid = $l10nParent->getUid();
        }

        $object = $this->getOrRemoveObjectByUidAndLanguageFromSession($uid, $desiredLanguage);
        if ($object !== null) {
            return $object;
        }

        $query = $this->createQuery();

        $this->changeLanguage($query, $desiredLanguage, LanguageAspect::OVERLAYS_ON);

        $query->getQuerySettings()
              ->setRespectSysLanguage(false);
        $query->matching($query->equals('l10n_parent', $uid));

        $result = $query->execute($getRawQuery);

        if ($getRawQuery) {
            return $result[0];
        }

        /** @var \Hausformat\Lib\Domain\Model\AbstractEntity $returnObject */
        $returnObject = $result->getFirst();
        return $returnObject;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\QueryInterface $query
     * @param                                               $languageId
     * @param                                               $overlayMode
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
     */
    protected function changeLanguage(QueryInterface &$query, $languageId, $overlayMode = null) {
        $languageAspect = $query->getQuerySettings()->getLanguageAspect();

        $newLanguageAspect = new LanguageAspect($languageId, $languageId, $overlayMode ?? $languageAspect->getOverlayType(), $languageAspect->getFallbackChain());

        $query->getQuerySettings()->setLanguageAspect($newLanguageAspect);
        return $query;
    }

    /**
     * @param int $uid
     * @param int $languageUid
     * @return AbstractEntity|null
     */
    protected function getOrRemoveObjectByUidAndLanguageFromSession($uid, $languageUid)
    {
        if ($this->persistenceSession->hasIdentifier('' . $uid, $this->objectType)) {
            /** @var AbstractEntity $location */
            $location = $this->persistenceSession->getObjectByIdentifier((string) $uid, $this->objectType);

            if ($location->_getProperty('_languageUid') === $languageUid) {
                return $location;
            } else {
                $this->persistenceSession->unregisterObject($location);
                $this->persistenceSession->unregisterReconstitutedEntity($location);
            }
        }
        return null;
    }

    /**
     * Finds an object matching the given identifier.
     *
     * @param integer $uid The identifier of the object to find
     * @param bool $getRawQuery
     *
     * @return object|null The matching object if found, otherwise NULL
     * @api
     */
    public function findByUid($uid, $getRawQuery = false)
    {
        $query = $this->createQuery();

        $query->matching($query->equals('uid', $uid));

        $returnArray = $query->execute($getRawQuery);

        if (count($returnArray) >= 1 && !$getRawQuery) {
            return $returnArray->getFirst();
        } elseif (count($returnArray) >= 1) {
            return $returnArray[0];
        }

        return null;
    }

    /**
     * returns the l10nParent for the provided object
     * return NULL if the provided object has no l10nParent
     *
     * @param \Hausformat\Lib\Domain\Model\AbstractEntity $object
     *
     * @return object|null
     */
    public function findL10nParent(\Hausformat\Lib\Domain\Model\AbstractEntity $object)
    {
        $parent = $object->getL10nParent();

        if (!$parent) {
            return null;
        }

        return $this->findByUidAndLanguage($parent, 0);
    }

    /**
     * @param int $uid
     * @param int $languageUid
     * @param bool $returnRawQueryResult
     *
     * @return mixed
     */
    public function findByUidAndLanguage(int $uid, int $languageUid, bool $returnRawQueryResult = false)
    {
        if (!$returnRawQueryResult) {
            $object = $this->getOrRemoveObjectByUidAndLanguageFromSession($uid, $languageUid);
            if ($object !== null) {
                return $object;
            }
        }

        $languageAspect = new LanguageAspect($languageUid);

        $query = $this->createQuery();

        $query->getQuerySettings()
              ->setRespectSysLanguage(false)
              ->setRespectStoragePage(false)
              ->setLanguageAspect($languageAspect);

        $result = $query
            ->matching($query->equals('uid', $uid))
            ->execute($returnRawQueryResult);

        if ($returnRawQueryResult) {
            return $result[0] ?? null;
        }
        $object = $result->getFirst();

        if($object instanceof TranslatableInterface && $object instanceof AbstractEntity && $object->getUid() === $object->getLocalizedUid()) {
            $rawQueryResult =  $query->execute(true)[0];
            if(isset($rawQueryResult['_PAGES_OVERLAY_UID'])) {
                $object->setLocalizedUid($rawQueryResult['_PAGES_OVERLAY_UID']);
            }
        }


        return $object;
    }

    /**
     * @param \Hausformat\Lib\Domain\Model\AbstractEntity $object
     * @param int $desiredLanguage
     * @param bool $returnRawQueryResult
     *
     * @return \Hausformat\Lib\Domain\Model\AbstractEntity|mixed
     */
    public function findTranslatedByUid(
        \Hausformat\Lib\Domain\Model\AbstractEntity $object,
        int $desiredLanguage,
        bool $returnRawQueryResult = false
    )
    {
        // current language already selected
        if ($object->getLanguageUid() === $desiredLanguage && !$returnRawQueryResult) {
            return $object;
        }

        return $this->findByUidAndLanguage($object->getUid(), $desiredLanguage, $returnRawQueryResult);
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\Session $persistenceSession
     */
    public function injectPersistenceSession(\TYPO3\CMS\Extbase\Persistence\Generic\Session $persistenceSession)
    {
        $this->persistenceSession = $persistenceSession;
    }

    /**
     * Removes an object from this repository.
     *
     * @param AbstractEntity $object The object to remove
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @api
     */
    public function remove($object)
    {

        if (!($object instanceof \Hausformat\Lib\Domain\Model\AbstractEntity)) {
            parent::remove($object);

            return;
        }

        if ($object->getLocalizedUid() != $object->getUid()) {
            $object->_setProperty('uid', $object->getLocalizedUid());
        } else {
            $dataMapper = GeneralUtility::makeInstance(DataMapper::class);
            $table = $dataMapper->convertClassNameToTableName($this->objectType);

            $where = '(sys_language_uid NOT IN (0) AND l10n_parent = ' . $object->getUid() . ') AND deleted=0 ';

            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

            $queryBuilder->delete($table)
                         ->where($where)
                         ->executeStatement();

        }

        parent::remove($object);

    }

    /**
     * @param int $defaultLimit
     */
    public function setDefaultLimit(int $defaultLimit)
    {
        $this->defaultLimit = $defaultLimit;
    }

    /**
     * @param int $defaultOffset
     */
    public function setDefaultOffset(int $defaultOffset)
    {
        $this->defaultOffset = $defaultOffset;
    }

    /**
     * @param bool $respectStoragePage
     */
    public function setRespectStoragePage(bool $respectStoragePage)
    {
        $this->respectStoragePage = $respectStoragePage;
    }

    /**
     * @param bool $respectSysLanguage
     */
    public function setRespectSysLanguage(bool $respectSysLanguage)
    {
        $this->respectSysLanguage = $respectSysLanguage;
    }

    /**
     * @param string $search
     * @param bool $returnRawQuery
     * @return array|QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function findBySearch(string $search, $returnRawQuery = false)
    {
        $searchFields = $this->getSearchFields();
        $query = $this->createQuery();

        $constraints = $query->getConstraint() ?? [];
        foreach ($searchFields as $searchField) {
            $this->getSearchConstraintsByString($searchField, $search, $constraints);
        }

        $query->matching($query->logicalOr($constraints));

        return $query->execute($returnRawQuery);
    }

    /**
     * @return array
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    protected function getSearchFields()
    {
        $persistence = $this->getPersistenceTS();

        if (isset($persistence[$this->objectType]['searchFields'])) {
            if (is_array($persistence[$this->objectType]['searchFields'])) {
                return $persistence[$this->objectType]['searchFields'];
            } elseif (is_string($persistence[$this->objectType]['searchFields'])) {
                return GeneralUtility::trimExplode(',', $persistence[$this->objectType]['searchFields']);
            }
        }
        return $this->getSearchFieldsFromTCA();
    }

    /**
     * @return array
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    protected function getSearchFieldsFromTCA()
    {
        $tableName = $this->getTableName();
        $tableTCA = GlobalsService::getInstance()->getTableTca($tableName);
        if (!isset($tableTCA['ctrl']['searchFields'])) {
            return [];
        }
        $searchfieldString = $tableTCA['ctrl']['searchFields'];
        return GeneralUtility::trimExplode(',', $searchfieldString);
    }

    /**
     * @return string|\TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMap
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    protected function getTableName()
    {
        $dataMapper = GeneralUtility::makeInstance(
            \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper::class
        );
        return $dataMapper->getDataMap($this->objectType)->getTableName();
    }

    /**
     * @param $fieldname
     * @param $string
     * @param $query
     * @param array $searchwordConstraints
     * @return array
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    protected function getSearchConstraintsByString($fieldname, $string, $query, &$searchwordConstraints = [])
    {
        $searchWords = GeneralUtility::trimExplode(' ', $string, true);

        foreach ($searchWords as $word) {
            $word = str_replace(['.', '-', ':', ';', '!', '?', ',', '(', ')', '[', ']', '\'', '"'], '_', $word);
            $word = trim($word, '_');
            $this->getSearchConstraintsFor($fieldname, $word, $query, $searchwordConstraints);
        }

        return $searchwordConstraints;
    }


    /**
     * @param string $fieldname
     * @param string $word
     * @param \TYPO3\CMS\Extbase\Persistence\QueryInterface $query
     * @param array $searchwordConstraints
     *
     * @return array
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    protected function getSearchConstraintsFor($fieldname, $word, $query, &$searchwordConstraints = [])
    {
        // Search for the Word
        $searchwordConstraints[] = $query->like($fieldname, $word);

        // Search for the Word at the Start of a String
        $searchwordConstraints[] = $query->like($fieldname, $word . '-%');
        $searchwordConstraints[] = $query->like($fieldname, $word . ' %');

        // Search for the Word at the end of a String
        $searchwordConstraints[] = $query->like($fieldname, '% ' . $word);
        $searchwordConstraints[] = $query->like($fieldname, '%-' . $word);

        // Word in the middle of a sentence
        $searchwordConstraints[] = $query->like($fieldname, '% ' . $word . ' %');

        // Words at the End of a Sentence
        $searchwordConstraints[] = $query->like($fieldname, '% ' . $word . '.%');
        $searchwordConstraints[] = $query->like($fieldname, '% ' . $word . '.%');
        $searchwordConstraints[] = $query->like($fieldname, '% ' . $word . '?%');
        $searchwordConstraints[] = $query->like($fieldname, '% ' . $word . '!%');
        $searchwordConstraints[] = $query->like($fieldname, '% ' . $word . ',%');
        $searchwordConstraints[] = $query->like($fieldname, '% ' . $word . ':%');
        $searchwordConstraints[] = $query->like($fieldname, '% ' . $word . ';%');

        // Word concated with other Words
        $searchwordConstraints[] = $query->like($fieldname, '%-' . $word . ' %');
        $searchwordConstraints[] = $query->like($fieldname, '% ' . $word . '-%');
        $searchwordConstraints[] = $query->like($fieldname, '%-' . $word . '.%');
        $searchwordConstraints[] = $query->like($fieldname, '%-' . $word . '?%');
        $searchwordConstraints[] = $query->like($fieldname, '%-' . $word . '!%');
        $searchwordConstraints[] = $query->like($fieldname, '%-' . $word . ',%');
        $searchwordConstraints[] = $query->like($fieldname, '%-' . $word . ':%');
        $searchwordConstraints[] = $query->like($fieldname, '%-' . $word . ';%');
        $searchwordConstraints[] = $query->like($fieldname, '%-' . $word . '-%');

        // Word in brace
        $searchwordConstraints[] = $query->like($fieldname, '%(' . $word . ')%');
        $searchwordConstraints[] = $query->like($fieldname, '%(' . $word . ' %');
        $searchwordConstraints[] = $query->like($fieldname, '%(' . $word . '-%');
        $searchwordConstraints[] = $query->like($fieldname, '% ' . $word . ')%');
        $searchwordConstraints[] = $query->like($fieldname, '%-' . $word . ')%');

        // Word in apostrophe
        $searchwordConstraints[] = $query->like($fieldname, '%\'' . $word . '\'%');
        $searchwordConstraints[] = $query->like($fieldname, '%\'' . $word . ' %');
        $searchwordConstraints[] = $query->like($fieldname, '% ' . $word . '\'%');
        $searchwordConstraints[] = $query->like($fieldname, '%\'' . $word . '-%');
        $searchwordConstraints[] = $query->like($fieldname, '%-' . $word . '\'%');

        // Word in apostrophe (‚ ‘)
        $searchwordConstraints[] = $query->like($fieldname, '%‚' . $word . '‘%');
        $searchwordConstraints[] = $query->like($fieldname, '%‚' . $word . ' %');
        $searchwordConstraints[] = $query->like($fieldname, '% ' . $word . '‘%');
        $searchwordConstraints[] = $query->like($fieldname, '%‚' . $word . '-%');
        $searchwordConstraints[] = $query->like($fieldname, '%-' . $word . '‘%');

        // Word in double apostrophe
        $searchwordConstraints[] = $query->like($fieldname, '%"' . $word . '"%');
        $searchwordConstraints[] = $query->like($fieldname, '%"' . $word . ' %');
        $searchwordConstraints[] = $query->like($fieldname, '%"' . $word . '-%');
        $searchwordConstraints[] = $query->like($fieldname, '% ' . $word . '"%');
        $searchwordConstraints[] = $query->like($fieldname, '%-' . $word . '"%');

        // Word in « »
        $searchwordConstraints[] = $query->like($fieldname, '%«' . $word . '»%');
        $searchwordConstraints[] = $query->like($fieldname, '%«' . $word . ' %');
        $searchwordConstraints[] = $query->like($fieldname, '%«' . $word . '-%');
        $searchwordConstraints[] = $query->like($fieldname, '% ' . $word . '»%');
        $searchwordConstraints[] = $query->like($fieldname, '%-' . $word . '»%');

        // Word in » «
        $searchwordConstraints[] = $query->like($fieldname, '%»' . $word . '«%');
        $searchwordConstraints[] = $query->like($fieldname, '%»' . $word . ' %');
        $searchwordConstraints[] = $query->like($fieldname, '%»' . $word . '-%');
        $searchwordConstraints[] = $query->like($fieldname, '% ' . $word . '«%');
        $searchwordConstraints[] = $query->like($fieldname, '%-' . $word . '«%');

        // Word in ‹ ›
        $searchwordConstraints[] = $query->like($fieldname, '%‹' . $word . '›%');
        $searchwordConstraints[] = $query->like($fieldname, '%‹' . $word . ' %');
        $searchwordConstraints[] = $query->like($fieldname, '%‹' . $word . '-%');
        $searchwordConstraints[] = $query->like($fieldname, '% ' . $word . '›%');
        $searchwordConstraints[] = $query->like($fieldname, '%-' . $word . '›%');

        // Word in › ‹
        $searchwordConstraints[] = $query->like($fieldname, '%›' . $word . '‹%');
        $searchwordConstraints[] = $query->like($fieldname, '%›' . $word . ' %');
        $searchwordConstraints[] = $query->like($fieldname, '%›' . $word . '-%');
        $searchwordConstraints[] = $query->like($fieldname, '% ' . $word . '‹%');
        $searchwordConstraints[] = $query->like($fieldname, '%-' . $word . '‹%');

        // Word in „ “
        $searchwordConstraints[] = $query->like($fieldname, '%„' . $word . '“%');
        $searchwordConstraints[] = $query->like($fieldname, '%„' . $word . ' %');
        $searchwordConstraints[] = $query->like($fieldname, '%„' . $word . '-%');
        $searchwordConstraints[] = $query->like($fieldname, '% ' . $word . '“%');
        $searchwordConstraints[] = $query->like($fieldname, '%-' . $word . '“%');

        // Word in “ ”
        $searchwordConstraints[] = $query->like($fieldname, '%“' . $word . '”%');
        $searchwordConstraints[] = $query->like($fieldname, '%“' . $word . ' %');
        $searchwordConstraints[] = $query->like($fieldname, '%“' . $word . '-%');
        $searchwordConstraints[] = $query->like($fieldname, '% ' . $word . '”%');
        $searchwordConstraints[] = $query->like($fieldname, '%-' . $word . '”%');

        // Word in “
        $searchwordConstraints[] = $query->like($fieldname, '%“' . $word . '“%');
        $searchwordConstraints[] = $query->like($fieldname, '% ' . $word . '“%');
        $searchwordConstraints[] = $query->like($fieldname, '%-' . $word . '“%');

        // Word in ”
        $searchwordConstraints[] = $query->like($fieldname, '%”' . $word . '”%');
        $searchwordConstraints[] = $query->like($fieldname, '%”' . $word . ' %');
        $searchwordConstraints[] = $query->like($fieldname, '%”' . $word . '-%');

        return $searchwordConstraints;
    }
}
