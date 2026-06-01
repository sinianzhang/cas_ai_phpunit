<?php declare(strict_types=1);

namespace Hausformat\ViewHelpers\ViewHelpers;

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

use Hausformat\Lib\Utility\ObjectStorageUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Check if a string/array/object contains a substring
 *
 * ## Examples
 *
 *     <hf:contains haystack="This is a string" needle="string" /> == true
 *     <hf:contains haystack="string string" needle="string" returnAmount="1" /> == 2
 *     <hf:contains haystack="{0: 'hello', 1: 'world'}" needle="world"/> == true
 *     <hf:contains haystack="{0: {0: 'hello', 1: 'world'}}" needle="world" path="0" /> == true
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class ContainsViewHelper extends AbstractViewHelper
{
    /**
     * @return bool|int
     */
    public function render(): bool|int
    {
        $haystack = $this->arguments['haystack'] ?? $this->renderChildren();
        $needle = $this->arguments['needle'];
        $returnAmount = $this->arguments['returnAmount'];
        $path = $this->arguments['path'];

        if ($path !== null) {
            $haystack = ObjectStorageUtility::getValueFromDottedPath($path, $haystack, true);
        }

        if (is_string($haystack)) {
            if ($returnAmount) {
                return substr_count($haystack, $needle);
            }
            return str_contains($haystack, $needle);
        }

        if($haystack instanceof ObjectStorage || $haystack instanceof QueryResult) {
            $haystack = $haystack->toArray();
        }

        if (!is_array($haystack)) {
            $haystack = (array) $haystack;
        }
        if ($returnAmount) {
            return count(array_filter($haystack, function ($val) use ($needle) {
                return $val === $needle;
            }));
        }
        return in_array($needle, $haystack);
    }

    /**
     * Initialize arguments.
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('haystack', 'mixed', 'What to search in', true);
        $this->registerArgument('needle', 'mixed', 'The needle to search for', true);
        $this->registerArgument('returnAmount', 'boolean', 'Return the amount of times the needle was found', false, false);
        $this->registerArgument('path', 'string', 'A subpath inside the haystack');
    }
}
