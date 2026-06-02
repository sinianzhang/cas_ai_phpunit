<?php

namespace Hausformat\Lib\Utility;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * ObjectStorage utility methods
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class ObjectStorageUtility
{

    const MODE_DEFAULT = 'default';
    const MODE_REVERSE = 'reverse';
    const MODE_SHUFFLE = 'shuffle';

    /**
     * @var string
     */
    protected static $orderByThisField = '';

    /**
     * Returns a filled objectStorage based on a queryResult
     *
     * @param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $queryResult
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public static function getOjectStorageFromQueryResult(\TYPO3\CMS\Extbase\Persistence\QueryResultInterface $queryResult)
    {
        return self::getOjectStorageFromIterable($queryResult);
    }


    /**
     * Returns a filled objectStorage based on an «iteratable» object or array
     *
     * @param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array|\Iterator|\TYPO3\CMS\Extbase\Persistence\ObjectStorage $iterable
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public static function getOjectStorageFromIterable($iterable)
    {
        if ($iterable instanceof \TYPO3\CMS\Extbase\Persistence\ObjectStorage) {
            return $iterable;
        }

        /** @var ObjectStorage $objectStorage */
        $objectStorage = GeneralUtility::makeInstance(ObjectStorage::class);

        if ($iterable !== null) {
            foreach ($iterable AS $object) {
                $objectStorage->attach($object);
            }
        }

        return $objectStorage;
    }

    /**
     * Returns a sorted, numerically indexed array based on the input parameters
     *
     * @param            $storage
     * @param            $field
     * @param array $totalsFor
     * @param null $sortItemsBy
     * @param null $sortGroupsBy
     * @param bool|array $groupSubs
     *
     * @return array
     */
    public static function groupBy(
        $storage,
        $field,
        $totalsFor = [],
        $sortItemsBy = null,
        $sortGroupsBy = null,
        $groupSubs = false
    )
    {
        $storage = $storage instanceof ObjectStorage ? $storage->toArray() : $storage;
        $array = [];

        foreach ($storage as $item) {
            $fieldValue = self::getValueFromDottedPath($field, $item);

            if ($fieldValue instanceof AbstractEntity || $fieldValue instanceof LazyLoadingProxy) {
                $identifier = $fieldValue->getUid();
            } else {
                $identifier = $fieldValue;
            }

            /** @var ObjectStorage $objectStorage */
            $objectStorage = GeneralUtility::makeInstance(ObjectStorage::class);
            $objectStorage->attach($item);

            if (!array_key_exists($identifier, $array)) {
                $array[$identifier] = [
                    'items' => $objectStorage,
                    'object' => $fieldValue,
                    'totals' => array_fill_keys($totalsFor, 0),
                ];
            }

            foreach ($totalsFor as $key) {
                $array[$identifier]['totals'][$key] += (float)self::getValueFromDottedPath($key, $item);
            }
        }

        // Loop through array and sort items
        if ($sortItemsBy !== null) {
            foreach ($array as $item) {
                $currentSortParams = array_merge([$item['items']], $sortItemsBy);

                $item['items'] = call_user_func_array([self::class, 'sortBy'], $currentSortParams);
            }
        }

        if ($sortGroupsBy !== null) {
            $sortGroupsBy = array_merge([$array], $sortGroupsBy);

            $array = call_user_func_array([self::class, 'sortBy'], $sortGroupsBy);
        }

        if ($groupSubs !== false) {
            foreach ($array as $key => $value) {
                $params = array_merge([$value['items']], $groupSubs);
                $array[$key]['items'] = call_user_func_array([self::class, 'groupBy'], $params);
            }
        }

        return array_values($array);
    }

    /**
     * Returns a value from a dotted path based on the given subject
     *
     * @param mixed $path
     * @param mixed $subject
     * @param bool $multipleAsArray
     *
     * @return mixed|null
     */
    static public function getValueFromDottedPath($path, $subject, $multipleAsArray = false)
    {
        $rawPath = $path;
        $path = is_array($path) ? $path : explode('.', $path);
        $key = array_shift($path);
        $lowerCamelCaseKey = GeneralUtility::underscoredToLowerCamelCase($key);

        switch (gettype($subject)) {
            case 'array':
                if (array_key_exists($key, $subject)) {
                    $value = $subject[$key];

                    if (count($path) > 0) {
                        return self::getValueFromDottedPath($path, $value, $multipleAsArray);
                    }

                    return $value;
                } elseif (array_key_exists($lowerCamelCaseKey, $subject)) {
                    $value = $subject[$lowerCamelCaseKey];

                    if (count($path) > 0) {
                        return self::getValueFromDottedPath($path, $value, $multipleAsArray);
                    }

                    return $value;
                } elseif ($multipleAsArray) {
                    $retArray = [];
                    foreach ($subject AS $key => $value) {
                        $retArray[] = self::getValueFromDottedPath($rawPath, $value, $multipleAsArray);
                    }

                    return $retArray;
                }
                break;
            case 'object':
                $getter = 'get' . ucfirst($key);
                $getter2 = 'get' . ucfirst($lowerCamelCaseKey);
                $value = null;

                if ($subject instanceof AbstractEntity) {
                    if (is_callable([$subject, $getter])) {
                        $value = $subject->{$getter}();
                    } elseif (is_callable([$subject, $getter2])) {
                        $value = $subject->{$getter2}();
                    } else {
                        if ($subject->_hasProperty($key)) {
                            $value = $subject->_getProperty($key);
                        } elseif ($subject->_hasProperty($lowerCamelCaseKey)) {
                            $value = $subject->_getProperty($lowerCamelCaseKey);
                        }
                    }

                    if (count($path) > 0) {
                        return self::getValueFromDottedPath($path, $value, $multipleAsArray);
                    }

                    return $value;
                }

                if ($subject instanceof ObjectStorage) {
                    return self::getValueFromDottedPath($rawPath, $subject->toArray(), $multipleAsArray);
                }

                if (is_callable([$subject, $getter])) {
                    $value = $subject->{$getter}();
                } else {
                    if (isset($subject->{$key})) {
                        $value = $subject->{$key};
                    } elseif (isset($subject->{$lowerCamelCaseKey})) {
                        $value = $subject->{$lowerCamelCaseKey};
                    }
                }

                if (count($path) > 0 && $value !== null) {
                    return self::getValueFromDottedPath($path, $value, $multipleAsArray);
                }

                return $value;
            case 'string':
            case 'integer':
            case 'boolean':
            case 'double':
                return $subject;
            default:
                if (count($path) == 0) {
                    return $subject;
                }
                break;
        }

        return null;
    }

    /**
     * @param mixed $path
     * @param mixed $subject
     * @param mixed $setValue
     *
     * @return mixed|null
     */
    static public function setValueOnDottedPath($path, &$subject, $setValue)
    {
        $rawPath = $path;
        $path = is_array($path) ? $path : explode('.', $path);
        $key = array_shift($path);

        try {
            switch (gettype($subject)) {
                case 'array':
                    if (count($path) > 0) {
                        if (!array_key_exists($key, $subject)) {
                            $subject[$key] = [];
                        }

                        return self::setValueOnDottedPath($path, $subject[$key], $setValue);
                    }
                    $subject[$key] = $setValue;

                    return true;
                case 'object':
                    $getter = 'get' . ucfirst($key);
                    $setter = 'set' . ucfirst($key);
                    $value = null;

                    if ($subject instanceof AbstractEntity) {
                        if (count($path) > 0) {
                            if (is_callable([$subject, $getter])) {
                                $value &= $subject->{$getter}();
                            } else {
                                if ($subject->_hasProperty($key)) {
                                    $value &= $subject->_getProperty($key);
                                }
                            }

                            return self::setValueOnDottedPath($path, $value, $setValue);
                        } else {
                            if (is_callable([$subject, $setter])) {
                                $subject->{$setter}($setValue);

                                return true;
                            } else {
                                if ($subject->_hasProperty($key)) {
                                    $subject->_setProperty($key, $setValue);

                                    return true;
                                }
                            }
                        }

                        return false;
                    }

                    if ($subject instanceof ObjectStorage) {
                        $storageAsArray = $subject->toArray();
                        if (is_numeric($key)) {
                            $objectToManipulate = $storageAsArray[$key];
                            $subject->detach($objectToManipulate);
                        } else {
                            $minimumOneManipulated = false;
                            foreach ($subject AS $storageKey => &$storageValue) {
                                $manipulated = self::setValueOnDottedPath($rawPath, $storageValue, $setValue);
                                if ($manipulated) {
                                    $minimumOneManipulated = true;
                                }
                            }

                            return $minimumOneManipulated;
                        }

                        if (count($path) > 0) {
                            $manipulated = self::setValueOnDottedPath($path, $objectToManipulate, $setValue);
                            if ($manipulated) {
                                $subject->attach($objectToManipulate);

                                return true;
                            }
                        } else {
                            $subject = $setValue;

                            return true;
                        }

                        return false;
                    }

                    if (is_callable([$subject, $getter])) {
                        $value &= $subject->{$getter}();
                    } else {
                        if (isset($subject->{$key})) {
                            $value &= $subject->{$key};
                        }
                    }

                    if (count($path) > 0 && $value !== null) {
                        return self::setValueOnDottedPath($path, $value, $setValue);
                    }

                    if (is_callable([$subject, $setter])) {
                        $subject->{$setter}($setValue);

                        return true;
                    } else {

                        if (property_exists(get_class($subject), $key)) {
                            $class = new \ReflectionClass(get_class($subject));
                            $property = $class->getProperty($key);
                            $property->setAccessible(true);
                            $subject->{$key} = $setValue;

                            return true;
                        }
                    }

                    return false;


                case 'string':
                case 'integer':
                case 'boolean':
                case 'double':
                    $subject = $setValue;

                    return true;

                default:
                    if (count($path) == 0) {
                        $subject = $setValue;

                        return true;
                    }

                    return false;

            }
        } catch (\Exception $e) {

        }

        return false;
    }

    /**
     * Insert an object before a given object
     *
     * @param ObjectStorage $objectStorage
     * @param                                              $objectToSet
     * @param                                              $objectToSetBefore
     *
     * @return ObjectStorage
     */
    public static function setPositionBefore(
        ObjectStorage $objectStorage,
        $objectToSet,
        $objectToSetBefore
    )
    {
        $position = $objectStorage->getPosition($objectToSetBefore);

        return self::setPosition($objectStorage, $objectToSet, $position);

    }

    /**
     * Set the position of a given object in the given objectStorage
     *
     * @param ObjectStorage $objectStorage
     * @param                                              $objectToSet
     * @param int $position
     *
     * @return ObjectStorage
     */
    public static function setPosition(ObjectStorage $objectStorage, $objectToSet, $position = 0)
    {
        $returnStorage = new ObjectStorage();

        $i = 0;
        foreach ($objectStorage AS $objectToInsert) {
            if ($i == $position) {
                $returnStorage->attach($objectToSet);
            }

            if ($objectToInsert != $objectToSet) {
                $returnStorage->attach($objectToInsert);
            }
            $i++;
        }

        return $returnStorage;
    }

    /**
     * Orders an objectStorage by a given field name
     *
     * @param ObjectStorage|array $objectStorage
     * @param String $orderByField
     * @param Bool|string $mode
     *
     * @return ObjectStorage|array
     */
    public static function sortBy($objectStorage, $orderByField, $mode = self::MODE_DEFAULT, $useStrComp = false)
    {
        $arrayToSort = is_array($objectStorage) ? $objectStorage : ($objectStorage instanceof ObjectStorage || $objectStorage instanceof QueryResultInterface ? $objectStorage->toArray() : []);

        self::$orderByThisField = $orderByField;
        usort($arrayToSort, function ($a, $b) use ($orderByField, $useStrComp) {
            return self::usortByParam($a, $b, $orderByField, $useStrComp);
        });

        if ($mode === self::MODE_REVERSE) {
            $arrayToSort = array_reverse($arrayToSort);
        }

        if ($mode === self::MODE_SHUFFLE) {
            shuffle($arrayToSort);
        }

        if (is_array($objectStorage)) {
            return $arrayToSort;
        }

        $returnStorage = new ObjectStorage();
        $newStep = 0;
        foreach ($arrayToSort AS $objectToInsert) {
            //$objectToInsert->_setProperty($orderByField, $newStep);
            $returnStorage->attach($objectToInsert);
            $newStep++;
        }
        //$objectStorage->removeAll($objectStorage);
        $objectStorage = $returnStorage;

        return $objectStorage;
    }

    /**
     * Returns a sorted array based on the 'USORT' method
     *
     * @param        $a
     * @param        $b
     * @param string $fieldName
     * @param bool   $useStrComp
     *
     * @return int
     */
    static public function usortByParam($a, $b, $fieldName = '', $useStrComp = false)
    {
        $aParam = self::getValueFromDottedPath($fieldName, $a);
        $bParam = self::getValueFromDottedPath($fieldName, $b);

        if(is_null($aParam) && is_null($bParam)) {
            return 0;
        } elseif (is_null($aParam)) {
            return -1;
        } elseif(is_null($bParam)) {
            return 1;
        }

        if ($useStrComp) {
            return self::strcmpspec($aParam, $bParam);
        } elseif (is_string($aParam)) {
            return self::strnatcasecmpspec($aParam, $bParam);
        } elseif (is_numeric($aParam)) {
            return ($aParam < $bParam) ? -1 : 1;
        } elseif ($aParam instanceof \Hausformat\Lib\Domain\Model\AbstractEntity) {
            if ($fieldName == '') {
                return ($aParam->getUid() < $bParam->getUid()) ? -1 : 1;
            } else {
                return self::usortByParam($aParam->_getProperty($fieldName), $bParam->_getProperty($fieldName));
            }

        } elseif (is_array($aParam)) {
            if ($fieldName != '') {
                return self::usortByParam($aParam[$fieldName], $bParam[$fieldName]);
            } else {
                return self::usortByParam(reset($aParam), reset($aParam));
            }
        } elseif (is_object($aParam)) {
            if ($aParam instanceof \DateTime && $bParam instanceof \DateTime && function_exists('gmp_cmp')) {
                return \gmp_cmp($aParam->getTimestamp(), $bParam->getTimestamp());
            } elseif($aParam instanceof \DateTime && $bParam instanceof \DateTime) {
                return $aParam->getTimestamp() > $bParam->getTimestamp() ? 1 : ($aParam->getTimestamp() < $bParam->getTimestamp() ? -1 : 0);
            }

            return 1;
        } elseif (is_bool($aParam)) {
            if ($aParam === (bool)$bParam) {
                return 0;
            } elseif ($aParam && !$bParam) {
                return 1;
            } else {
                return -1;
            }
        }

        if ($aParam == null || $bParam == null) {
            return 0;
        }

        return ($aParam->getUid() < $bParam->getUid()) ? -1 : 1;
    }

    /**
     * @param string $a
     * @param string $b
     * @return int
     */
    static protected function strnatcasecmpspec($a, $b)
    {
        // Sonderzeichen ersetzen
        $a = StringUtility::sanitizeOrderableString($a);
        $b = StringUtility::sanitizeOrderableString($b);
        return strnatcasecmp($a, $b);
    }

    /**
     * @param string $a
     * @param string $b
     * @return int
     */
    static protected function strcmpspec($a, $b)
    {
        // Sonderzeichen ersetzen
        $a = StringUtility::sanitizeOrderableString($a);
        $b = StringUtility::sanitizeOrderableString($b);
        return strcmp($a, $b);
    }

}
