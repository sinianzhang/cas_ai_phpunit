<?php

namespace Hausformat\Lib\Hooks;

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
use Hausformat\Lib\Utility\TypoScriptUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Defines standard functions which can be used in TCA/FlexForm to fill select fields with predefined configurations.
 * e.g. template layouts or TypoScriptItems
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class ItemsProcFunc
{

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * Overwrite this Parameter in Your Extension with the Extensionkey or wathever You want.
     * The Result of this Key is, that you use it in the PageTS or in the TYPO3_CONF_VARS
     *
     * i.E.
     * $extensionName = 'myextension';
     *
     * Use this in PageTS like this (global):
     * tx_myextension.templateLayouts {
     *      10 = My Template Layout
     * }
     *
     * Or with switchableControllerActions like this:
     * tx_myextension.list.templateLayouts {
     *      10 = A List Template Layout
     *      20 = A other List Template Layout
     * }
     *
     * @var string
     */
    protected $extensionName = 'hfextbase';

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var string
     */
    protected $tablename;

    /**
     * Itemsproc function to extend the selection of templateLayouts in the plugin
     *
     * @param array &$config configuration array
     *
     * @return void
     */
    public function user_templateLayout(array &$config)
    {
        $this->setConfigurationManager();
        $flexformArray = $config['flexParentDatabaseRow']['pi_flexform'];

        if (isset($flexformArray['data']['sDEF']['lDEF']['switchableControllerActions']['vDEF'])) {
            $switchableControllerAction = $flexformArray['data']['sDEF']['lDEF']['switchableControllerActions']['vDEF'];

            $allActions = explode(';', $switchableControllerAction);
            $actionName = explode('->', $allActions[0])[1];
        } else {
            $actionName = 'noSwitchableControllerAction';
        }

        $languageService = GlobalsService::getInstance()->getBackendLanguageService();
        $confVars = GlobalsService::getInstance()->getConfVars();

        // Check if the layouts are extended by ext_tables
        if (isset($confVars['EXT'][$this->extensionName]['templateLayouts'])
            && is_array($confVars['EXT'][$this->extensionName]['templateLayouts'])
        ) {

            // Add every item
            foreach ($confVars['EXT'][$this->extensionName]['templateLayouts'] as $layouts) {
                $tempLable = $languageService->sL($layouts[0]);
                if ($tempLable == '' || $tempLable == null) {
                    $tempLable = $this->getLLVarFromTS($layouts[0]);
                }
                $additionalLayout = [
                    $tempLable,
                    $layouts[1],
                ];
                array_push($config['items'], $additionalLayout);
            }
        }

        // And now if there are some special templateLayouts only for this view then now add it!
        if (isset($confVars['EXT'][$this->extensionName][$actionName]['templateLayouts'])
            && is_array($confVars['EXT'][$this->extensionName][$actionName]['templateLayouts'])
        ) {

            // Add every item
            foreach ($confVars['EXT'][$this->extensionName][$actionName]['templateLayouts'] as $layouts) {
                $tempLable = $languageService->sL($layouts[0]);
                if ($tempLable == '' || $tempLable == null) {
                    $tempLable = $this->getLLVarFromTS($layouts[0]);
                }
                $additionalLayout = [
                    $tempLable,
                    $layouts[1],
                ];
                array_push($config['items'], $additionalLayout);
            }
        }

        if (isset($config['flexParentDatabaseRow']['pid']) && !isset($config['row']['pid'])) {
            $config['row']['pid'] = $config['flexParentDatabaseRow']['pid'];
        }

        // Add tsconfig values
        if (is_numeric($config['row']['pid'])) {
            $pagesTsConfig = BackendUtility::getPagesTSconfig($config['row']['pid']);
            if (isset($pagesTsConfig['tx_' . $this->extensionName . '.']['templateLayouts.']) && is_array($pagesTsConfig['tx_' . $this->extensionName . '.']['templateLayouts.'])) {

                // Add every item
                foreach ($pagesTsConfig['tx_' . $this->extensionName . '.']['templateLayouts.'] as $key => $label) {
                    $tempLable = $languageService->sL($label);
                    if ($tempLable == '' || $tempLable == null) {
                        $tempLable = $this->getLLVarFromTS($label);
                    }
                    $additionalLayout = [
                        $tempLable,
                        $key,
                    ];
                    array_push($config['items'], $additionalLayout);
                }
            }

            // if there Are some Special Layouts only for this View then Add it
            if (isset($pagesTsConfig['tx_' . $this->extensionName . '.'][$actionName . '.']['templateLayouts.']) && is_array($pagesTsConfig['tx_' . $this->extensionName . '.'][$actionName . '.']['templateLayouts.'])) {

                // Add every item
                foreach ($pagesTsConfig['tx_' . $this->extensionName . '.'][$actionName . '.']['templateLayouts.'] as $key => $label) {
                    $tempLable = $languageService->sL($label);
                    if ($tempLable == '' || $tempLable == null) {
                        $tempLable = $this->getLLVarFromTS($label);
                    }
                    $additionalLayout = [
                        $tempLable,
                        $key,
                    ];
                    array_push($config['items'], $additionalLayout);
                }
            }
        }

        if(! is_array($config['items'])) {
            $config['items'] = [];
        }

    }

    /**
     * Returns the localized local lang variable
     *
     * @param string $label
     * @param string $tempExtensionName
     *
     * @return string
     */
    protected function getLLVarFromTS($label, $tempExtensionName = '')
    {
        $this->setConfigurationManager();
        $localizedText = '';
        if ($tempExtensionName == '') {
            $tempExtensionName = $this->extensionName;
        }
        $tempLable = $label;
        if (str_contains($label, 'LLL') || str_contains($label, 'EXT')) {
            $arrayLable = GeneralUtility::trimExplode(':', $label);
            $tempLable = array_pop($arrayLable);
            if (!str_contains($label, $tempExtensionName)) {
                $filePathArray = GeneralUtility::trimExplode('/', array_pop($arrayLable));

                $tempExtensionName = $filePathArray[0];
            }
        }

        $fullTypoScript = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $extensionLLSettings = $fullTypoScript['plugin.']['tx_' . $tempExtensionName . '.'];

        if (isset($extensionLLSettings['_LOCAL_LANG.'])) {
            $extensionLLSettings = $extensionLLSettings['_LOCAL_LANG.'];
        } else {
            return '';
        }

        $lang = $this->getLocale();

        if (isset($extensionLLSettings[$lang . '.'])) {
            $extensionLLSettings = $extensionLLSettings[$lang . '.'];
        } elseif (isset($extensionLLSettings['default.'])) {
            $extensionLLSettings = $extensionLLSettings['default.'];
        } else {
            return '';
        }

        if (isset($extensionLLSettings[$tempLable])) {
            $localizedText = $extensionLLSettings[$tempLable];
        } else {
            $tempLableArray = GeneralUtility::trimExplode('.', $tempLable);
            $tempArray = $extensionLLSettings;
            foreach ($tempLableArray AS $subLangPart) {
                if (isset($tempArray[$subLangPart . '.'])) {
                    $tempArray = $tempArray[$subLangPart . '.'];
                } elseif (isset($tempArray[$subLangPart])) {
                    $tempArray = $tempArray[$subLangPart];
                }
            }

            if (is_string($tempArray)) {
                $localizedText = $tempArray;
            }
        }

        return $localizedText;
    }

    /**
     * Itemsproc function to extend the selection of an List with Items in TCA
     *
     * @param array &$config configuration array
     *
     * @return void
     */
    public function user_typoScriptItems(array &$config)
    {
        $this->setConfigurationManager();
        $this->setSettings();

        $field = $config['field'];

        if (isset($config['table'])) {
            $this->tablename = $config['table'];
        }

        if ($this->extensionName == 'hfextbase' && $this->tablename) {
            $tableArray = GeneralUtility::trimExplode('_', $config['table']);
            if ($tableArray[0] == 'tx' && $tableArray[2] == 'domain') {
                $this->extensionName = $tableArray[1];
            }
        }


        if(str_contains($field, '.')) {
            $fieldArray = GeneralUtility::trimExplode('.', $field);
            $field = array_pop($fieldArray);
        }

        $languageService = GlobalsService::getInstance()->getBackendLanguageService();

        $confArray = [];
        if (isset($this->settings['addItems'][$this->tablename]) && is_array($this->settings['addItems'][$this->tablename])) {
            $confArray = $this->settings['addItems'][$this->tablename];
        }

        if (isset($this->settings['addItems']) && $confArray == []) {
            $confArray = $this->settings['addItems'];
        }

        if (isset($confArray[$field]) && is_array($confArray[$field])) {
            foreach ($confArray[$field] AS $key => $label) {
                $tempExtensionName = $this->extensionName;
                $tempLable = $label;
                if (str_contains($label, 'LLL') || str_contains($label, 'EXT')) {
                    $arrayLable = GeneralUtility::trimExplode(':', $label);
                    $tempLable = array_pop($arrayLable);
                    if (strpos($label, $this->extensionName) === false) {
                        $filePathArray = GeneralUtility::trimExplode('/', array_pop($arrayLable));

                        $tempExtensionName = $filePathArray[0];
                    }
                }

                $localizedText = $this->getLLVarFromTS($tempLable, $tempExtensionName);
                if ($localizedText == '' || $localizedText == null) {
                    $localizedText = $languageService->sL($label);
                }
                if ($localizedText == '' || $localizedText == null) {
                    $localizedText = LocalizationUtility::translate($tempLable, $tempExtensionName);
                }

                $additionalOptions = [
                    $localizedText,
                    $key,
                ];
                array_push($config['items'], $additionalOptions);
            }
        }
    }

    /**
     * Sets the settings
     */
    protected function setSettings()
    {
        $this->settings = TypoScriptUtility::getSettingsForExtension($this->extensionName);
    }

    /**
     * Sets the configurationManager
     */
    protected function setConfigurationManager()
    {
        if ($this->configurationManager == null) {
            $this->configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);

        }
    }

    protected function getLocale(): string
    {
        $languageServiceFactory = GeneralUtility::makeInstance(
            LanguageServiceFactory::class
        );
        // As we are in a static context we cannot get the current request in
        // another way this usually points to general flaws in your software-design
        $request = $GLOBALS['TYPO3_REQUEST'];
        $languageService = $languageServiceFactory->createFromSiteLanguage(
            $request->getAttribute('language')
            ?? $request->getAttribute('site')->getDefaultLanguage()
        );
        return $languageService->getLocale()->getLanguageCode();
    }
}
