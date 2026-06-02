<?php

namespace Hausformat\Lib\Configuration\Loader;

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

/**
 * The YamlFileLoader class loads and parses YAML configuration files, supporting dynamic value substitution through $...$ placeholders.
 * It recursively resolves placeholders using a reference array, ensuring flexible and dynamic configuration management.
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class YamlFileLoader
{
    /**
     * Loads and parses a YAML file, and returns an array with the found data
     *
     * @param string $fileName either relative to PATH_site or prefixed with EXT:...
     *
     * @return array the configuration as array
     * @throws \RuntimeException when the file is empty or is of invalid format
     */
    public function load(string $fileName): array
    {
        $fileLoader = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader::class);
        $content = $fileLoader->load($fileName);

        // Check for "$" placeholders
        $content = $this->processPlaceholders($content, $content);

        return $content;
    }

    /**
     * Main function that gets called recursively to check for %...% placeholders
     * inside the array
     *
     * @param array $content the current sub-level content array
     * @param array $referenceArray the global configuration array
     *
     * @return array the modified sub-level content array
     */
    protected function processPlaceholders(array $content, array $referenceArray): array
    {
        foreach ($content as $k => $v) {
            if ($this->isPlaceholder($v)) {
                $content[$k] = $this->getValueFromReferenceArray($v, $referenceArray);
            } elseif (is_array($v)) {
                $content[$k] = $this->processPlaceholders($v, $referenceArray);
            }
        }

        return $content;
    }

    /**
     * Checks if a value is a string and begins and ends with $...$
     *
     * @param mixed $value the probe to check for
     *
     * @return bool
     */
    protected function isPlaceholder($value): bool
    {
        return is_string($value) && substr($value, 0, 1) === '$' && substr($value, -1) === '$';
    }

    /**
     * Returns the value for a placeholder as fetched from the referenceArray
     *
     * @param string $placeholder the string to search for
     * @param array $referenceArray the main configuration array where to look up the data
     *
     * @return array|mixed|string
     */
    protected function getValueFromReferenceArray(string $placeholder, array $referenceArray)
    {
        $pointer = trim($placeholder, '$');
        $parts = explode('.', $pointer);
        $referenceData = $referenceArray;
        foreach ($parts as $part) {
            if (isset($referenceData[$part])) {
                $referenceData = $referenceData[$part];
            } else {
                // return unsubstituted placeholder
                return $placeholder;
            }
        }
        if ($this->isPlaceholder($referenceData)) {
            $referenceData = $this->getValueFromReferenceArray($referenceData, $referenceArray);
        }

        if (is_array($referenceData)) {
            return $this->processPlaceholders($referenceData, $referenceArray);
        }

        return $referenceData;
    }
}
