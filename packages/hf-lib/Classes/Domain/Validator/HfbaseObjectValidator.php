<?php
namespace Hausformat\Lib\Domain\Validator;

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

use Hausformat\Lib\Domain\Model\AbstractEntity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;
use TYPO3\CMS\Extbase\Validation\Validator\NotEmptyValidator;

/**
 * An ObjectValidator for use in controller classes to validate an AbstractEntity against its validation definitions.
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class HfbaseObjectValidator extends AbstractValidator
{
    /**
     * Configuration Manager
     *
     * @var ConfigurationManagerInterface
     *
     */
    protected $configurationManager;

    /**
     * @var array
     */
    protected $errorFields = [];

    /**
     * @var string
     */
    protected $extensionKey = 'hfextbase';

    /**
     * @var array
     */
    protected $generalSettings = [];

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var AbstractEntity
     */
    protected $value = null;

    public function initializeObject()
    {
        $this->setGeneralSettings();
    }

    /**
     * Sets the general settings
     */
    protected function setGeneralSettings()
    {
        if (empty($this->generalSettings)) {
            $this->generalSettings = $this->configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
                $this->extensionKey
            );
        }
    }

    /**
     * @param ConfigurationManagerInterface $configurationManager
     */
    public function injectConfigurationManager(
        ConfigurationManagerInterface $configurationManager
    ) {
        $this->configurationManager = $configurationManager;
    }

    /**
     * Returns if the $value is valid
     *
     * @param mixed $value
     *
     * @return void
     */
    protected function isValid($value): void
    {
        $this->value = $value;
        $sucsess = false;
        $this->getValidationConfig();

        if (!isset($this->settings['fields'])) {
            return;
        }

        foreach ($this->settings['fields'] as $fieldname => $validationConfig) {

            if (isset($validationConfig['validator']) && !empty($validationConfig['validator'])) {
                $options = [];
                if (isset($validationConfig['options'])) {
                    $options = $validationConfig['options'];
                }
                /**
                 * @var AbstractValidator $validator
                 */
                $validator = GeneralUtility::makeInstance($validationConfig['validator'], $options);
                $fieldValue = $this->value->_getProperty($fieldname);

                $validationresult = $validator->validate($fieldValue);

                if ($validationresult->hasErrors()) {
                    $this->errorFields[] = $fieldname;
                    if (isset($validationConfig['errorMessage'])) {
                        /**
                         * @var Error $newError
                         */
                        $newError = GeneralUtility::makeInstance(Error::class,
                            ['message' => $validationConfig['errorMessage'], 40213131]);
                        $this->result->forProperty($fieldname)->addError($newError);
                    } else {
                        $this->result->forProperty($fieldname)->addError($validationresult->getFirstError());
                    }
                }
            }

        }

        $requiredFields = explode(',', $this->settings['required']);
        foreach ($requiredFields as $oneRequiredField) {

            if (in_array($oneRequiredField, $this->errorFields)) {
                continue;
            }

            $validator = GeneralUtility::makeInstance(NotEmptyValidator::class);
            $fieldValue = $this->value->_getProperty($oneRequiredField);
            $validationresult = $validator->validate($fieldValue);

            if ($validationresult->hasErrors()) {
                $this->result->forProperty($oneRequiredField)->addError($validationresult->getFirstError());
            }

        }

        return;
    }

    /**
     * Gets the validation configuration
     *
     * @return void
     */
    protected function getValidationConfig()
    {
        if (is_object($this->value)) {
            $objectType = get_class($this->value);
            $this->settings = $this->generalSettings[$objectType]['validation'];
        }
    }

}
