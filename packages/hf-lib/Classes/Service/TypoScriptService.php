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

use Hausformat\Lib\Service\Interfaces\TypoScriptServiceInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Service class for managing and retrieving TypoScript configurations in TYPO3.
 * Provides methods for accessing full TypoScript arrays or specific subsets, as well as plugin-specific settings.
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class TypoScriptService implements TypoScriptServiceInterface
{
    /**
     * @var ConfigurationManagerInterface
     *
     */
    protected $configurationManager;

    /**
     * @var array
     */
    protected $full = null;

    /**
     * @var \TYPO3\CMS\Core\TypoScript\TypoScriptService
     *
     */
    protected $typoScriptService;

    /**
     * @var array
     */
    private $config;

    /**
     * Returns an instance of TypoScriptService
     *
     * @return TypoScriptService|object
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(self::class);
    }

    /**
     * Returns the full typoscript config (or a subset of it)
     *
     * @param string $subset optional Subset of the typoscript array
     *
     * @return mixed
     */
    public function getFullConfig($subset = null)
    {
        if ($this->full === null) {
            $this->full = $this->typoScriptService->convertTypoScriptArrayToPlainArray($this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT));
        }

        if ($subset !== null) {
            return $this->getTyposcriptSubset($subset, $this->full);
        }

        return $this->full;
    }

    /**
     * Returns a subset from the config array
     *
     * @param mixed $subset a typoscript path (x.y.z) or an array array( x,y,z )
     * @param array $settings the typoscript settings to fetch from
     *
     * @return mixed
     */
    protected function getTyposcriptSubset($subset, $settings)
    {
        // cancel if settings arent provided
        if (empty($settings) || !is_array($settings)) {
            return null;
        }
        // convert subset into array
        if (!is_array($subset)) {
            $subset = explode('.', $subset);
        }
        // get current subset
        $currentSubset = array_shift($subset);
        if (array_key_exists($currentSubset, $settings)) {
            // call recursively
            if (count($subset)) {
                return $this->getTyposcriptSubset($subset, $settings[$currentSubset]);
            } // return current value
            else {
                return $settings[$currentSubset];
            }
        }

        return null;
    }

    /**
     * Returns the plugin name
     *
     * @return string
     */
    public function getPluginName()
    {
        return $this->getConfig()['pluginName'];
    }

    /**
     * Returns the configuration
     *
     * @return array
     */
    protected function getConfig()
    {
        if ($this->config === null) {
            $this->config = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        }

        return $this->config;
    }

    /**
     * Returns a specific typoscript key from the settings section
     *
     * @param string $key the settings key
     *
     * @return mixed
     */
    public function getSetting($key)
    {
        return $this->getFrameworkConfig('settings.' . $key);
    }

    /**
     * Returns the typoscript settings
     *
     * @param string $subset optional Subset of the typoscript array
     *
     * @return mixed
     */
    public function getFrameworkConfig($subset = null)
    {
        if ($subset !== null) {
            return $this->getTyposcriptSubset($subset, $this->getConfig());
        }

        return $this->getConfig();
    }

    /**
     * @param \TYPO3\CMS\Core\TypoScript\TypoScriptService $typoScriptService
     */
    public function injectTypoScriptService(\TYPO3\CMS\Core\TypoScript\TypoScriptService $typoScriptService)
    {
        $this->typoScriptService = $typoScriptService;
    }

    /**
     * @param ConfigurationManagerInterface $configurationManager
     */
    public function injectConfigurationManager(
        ConfigurationManagerInterface $configurationManager
    )
    {
        $this->configurationManager = $configurationManager;
    }

}
