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

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Cache\CacheDataCollector;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service class for accessing and managing global TYPO3 variables, including configuration variables, backend and frontend users, and TCA settings.
 * Provides utility methods for interacting with global state within the TYPO3 framework.
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class GlobalsService implements SingletonInterface
{
    /**
     * Returns an instance of GlobalsService
     *
     * If the single instance does not yet exist, create it
     * Then return the single instance
     *
     * @return GlobalsService|object
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(self::class);
    }

    /**
     * @param string $key
     *
     * @return mixed
     * @internal
     */
    public function __getGlobal($key)
    {
        return $this->getGlobal($key);
    }

    /**
     *  Returns a specific value for a key out of the globals
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function getGlobal($key)
    {
        if (!$this->hasGlobal($key)) {
            throw new \InvalidArgumentException('Value for key "' . $key . '" not found');
        }

        return $GLOBALS[$key];
    }

    /**
     * Returns if a key exists in the globals
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasGlobal($key)
    {
        return isset($GLOBALS[$key]);
    }

    /**
     * Returns the back path
     *
     * @return string
     */
    public function getBackPath()
    {
        return $this->getGlobal('BACK_PATH');
    }

    /**
     * Returns the active be_user
     *
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    public function getBackendUser()
    {
        return $this->getGlobal('BE_USER');
    }

    /**
     * Returns a specific var from TYPO3_CONF_VARS
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getConfVar($name)
    {
        $vars = $this->getConfVars();

        if ($this->hasConfVar($name)) {
            return $vars[$name];
        }

        throw new \InvalidArgumentException('Key ' . $name . ' not found in TYPO3_CONF_VARS');
    }

    /**
     * Returns the TYPO3_CONF_VARS
     *
     * @return array
     */
    public function getConfVars()
    {
        return $this->getGlobal('TYPO3_CONF_VARS');
    }

    /**
     * Returns, if a specific var exists in TYPO3_CONF_VARS
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasConfVar($name)
    {
        $vars = $this->getConfVars();

        return isset($vars[$name]);
    }

    /**
     * Returns the EXEC_TIME as DateTime object
     *
     * @return \DateTime
     */
    public function getExecDateTime()
    {
        $timestamp = $this->getExecTimestamp();

        $dateTime = new \DateTime();
        $dateTime->setTimestamp($timestamp);

        return $dateTime;
    }

    /**
     * Returns the EXEC_TIME
     *
     * @return int
     */
    public function getExecTimestamp()
    {
        return $this->getGlobal('EXEC_TIME');
    }

    /**
     * Returns the actual fe_user
     *
     * @return \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication|null
     */
    public function getFrontendUser()
    {
        if($this->getTypo3Request() == null) {
            return null;
        }
        return $this->getTypo3Request()->getAttribute('frontend.user');
    }

    /**
     * @return CacheDataCollector
     */
    public function getCacheDataCollector(): CacheDataCollector
    {
        $request = $this->getTypo3Request();
        return $request->getAttribute('frontend.cache.collector');
    }

    /**
     * Returns the LanguageService
     *
     * @return \TYPO3\CMS\Core\Localization\LanguageService
     */
    public function getLanguageService($locale = 'default')
    {
        return GeneralUtility::makeInstance(LanguageServiceFactory::class)->create($locale);
    }

    /**
     * Returns the LanguageService via Backend User
     *
     * @return \TYPO3\CMS\Core\Localization\LanguageService
     */
    public function getBackendLanguageService()
    {
        return GeneralUtility::makeInstance(LanguageServiceFactory::class)
                            ->createFromUserPreferences($GLOBALS['BE_USER']);
    }

    /**
     * Returns the extension's TCA from the TYPO3 globals.
     *
     * @param string $tableName
     *
     * @return array
     * @api
     */
    public function getTableTca($tableName)
    {
        return $this->getTca()[$tableName];
    }

    /**
     * @return ServerRequestInterface
     */
    public function getTypo3Request() {
        return $this->getGlobal('TYPO3_REQUEST');
    }

    /**
     * Returns the TCA
     *
     * @return array
     */
    public function getTca()
    {
        return $this->getGlobal('TCA');
    }

    /**
     * Returns the extension's TCA from the TYPO3 globals.
     *
     * @param string $tableName
     * @param string $fieldName
     *
     * @return array
     * @api
     */
    public function getTableTcaField($tableName, $fieldName)
    {
        $tca = $this->getTableTcaFields($tableName);

        if (!isset($tca[$fieldName])) {
            throw new \InvalidArgumentException('Field definition for key "' . $fieldName . '" not found');
        }

        return $tca[$fieldName];
    }

    /**
     * Returns the extension's TCA from the TYPO3 globals.
     *
     * @param string $tableName
     *
     * @return array
     * @api
     */
    public function getTableTcaFields($tableName)
    {
        $tca = $this->getTca();

        if (!isset($tca[$tableName])) {
            throw new \InvalidArgumentException('Table definition for key "' . $tableName . '" not found');
        }

        return $tca[$tableName]['columns'];
    }

    /**
     * @return int
     */
    public function getFrontendPid(): int
    {
        if (($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
            && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend()) {
            $pageInformation = $GLOBALS['TYPO3_REQUEST']->getAttribute('frontend.page.information');
            $pid = $pageInformation->getId();
        } else {
            $pid = 0;
        }
        return $pid;
    }
}
