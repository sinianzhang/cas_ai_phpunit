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

use Hausformat\Lib\Service\GlobalsService;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Collection of TypoScript related utility methods
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class TypoScriptUtility
{

    /**
     * Returns the TypoScript settings as plain array
     *
     * @param array $settings
     *
     * @return array
     */
    public static function getPlainArray($settings)
    {
        return self::cleanSettings($settings);
    }

    /**
     * Returns the typoScript settings as plain array
     *
     * @param array $settings
     *
     * @return array
     */
    public static function cleanSettings($settings): array
    {
        if ($settings == null) return [];

        $typoScriptService = GeneralUtility::makeInstance(\TYPO3\CMS\Core\TypoScript\TypoScriptService::class);

        return $typoScriptService->convertTypoScriptArrayToPlainArray($settings);
    }

    /**
     * Returns the settings for a specific plugin/extension
     *
     * @param string $extensionName
     * @param null|string $pluginName
     * @param bool $settingsForBackend
     *
     * @return array
     */
    public static function getSettingsForExtension($extensionName, $pluginName = null, $settingsForBackend = false)
    {
        if (strpos($extensionName, 'tx_') === 0) {
            $extensionName = str_replace('tx_', '', $extensionName);
        }
        $configurationManager = self::getConfigurationManager();

        if ($settingsForBackend) {
            try {
                $settings = self::getTypoScriptsPartFromExtension($extensionName, 'module', $pluginName);
            } catch(\Exception $e) {
                $settings = null;
            }
        } else {
            try {
                $settings = $configurationManager->getConfiguration(
                    ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
                    $extensionName,
                    $pluginName
                );
            } catch(\Exception $e) {
                $settings = null;
            }
        }

        if (!$settings) {
            $settings = self::getTypoScriptsPartFromExtension($extensionName, 'plugin', $pluginName);
        }

        return $settings;
    }

    /**
     * Returns the ConfigurationManager
     *
     * @return object|ConfigurationManagerInterface
     */
    protected static function getConfigurationManager()
    {

        if (!(GlobalsService::getInstance()->hasGlobal('TSFE') || isset($_GET['id'])) && isset($_GET['returnUrl'])) {
            $encodedUrl = str_replace('?', '', urldecode($_GET['returnUrl']));
            /** @var string $encodedUrl */
            $requestParams = GeneralUtility::explodeUrl2Array($encodedUrl);

            if (isset($requestParams['id'])) {
                $_GET['id'] = $requestParams['id'];
                $GLOBALS['HTTP_GET_VARS']['id'] = $requestParams['id'];
            }
        }

        return GeneralUtility::makeInstance(ConfigurationManagerInterface::class);
    }

    /**
     * @param        $extensionName
     * @param string $mode
     * @param null $pluginName
     * @param string $configurationType Allowed: framework, settings, persistence, view (also fullTypoScript, but this returns the same as framework)
     *
     * @return array
     */
    public static function getTypoScriptsPartFromExtension($extensionName, $mode = 'plugin', $pluginName = null, $configurationType = ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS)
    {
        $configurationManager = self::getConfigurationManager();
        $typoScriptPart = [];
        if ($pluginName !== null) {
            $extensionKey = strtolower($extensionName) . '_' . $pluginName;
        } else {
            $extensionKey = strtolower($extensionName);
        }
        $fullConfiguration = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        if (isset($fullConfiguration[$mode . '.']['tx_' . $extensionKey . '.'])) {
            switch (strtolower($configurationType)) {
                case strtolower(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK):
                case strtolower(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT):
                    $part = $fullConfiguration[$mode . '.']['tx_' . $extensionKey . '.'];
                    break;
                case 'persistence':
                    $part = $fullConfiguration[$mode . '.']['tx_' . $extensionKey . '.']['persistence.'];
                    break;
                case 'view':
                    $part = $fullConfiguration[$mode . '.']['tx_' . $extensionKey . '.']['view.'];
                    break;
                default:
                    $part = $fullConfiguration[$mode . '.']['tx_' . $extensionKey . '.']['settings.'];
            }
            $typoScriptPart = self::cleanSettings($part);
        }

        return $typoScriptPart;
    }

    /**
     * Returns the settings for a specific plugin/extension
     *
     * @param string $extensionName
     * @param null|string $pluginName
     * @param bool $settingsForBackend
     *
     * @return array
     */
    public static function getFrameworkConfigurationForExtension($extensionName, $pluginName = null, $settingsForBackend = false)
    {
        if (strpos($extensionName, 'tx_') === 0) {
            $extensionName = str_replace('tx_', '', $extensionName);
        }
        $configurationManager = self::getConfigurationManager();

        if ($settingsForBackend) {
            $settings = self::getTypoScriptsPartFromExtension($extensionName, 'module', $pluginName, ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        } else {
            $settings = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
                $extensionName, $pluginName);
        }

        if (!$settings) {
            $settings = self::getTypoScriptsPartFromExtension($extensionName, 'plugin', $pluginName, ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        }

        return $settings;
    }

    /**
     * Returns a settings-array as typoScript-array
     *
     * @param array $settings
     *
     * @return array
     */
    public static function getTypoScriptArray($settings)
    {
        $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);

        return $typoScriptService->convertPlainArrayToTypoScriptArray($settings);
    }

    /**
     * Returns the settings for this plugin/extension
     *
     * @return array
     */
    public static function getTypoScriptSettings()
    {
        $configurationManager = self::getConfigurationManager();

        return $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS);
    }

}
