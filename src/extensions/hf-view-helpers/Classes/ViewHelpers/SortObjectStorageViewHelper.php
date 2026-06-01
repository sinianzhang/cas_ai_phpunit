<?php

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
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Sorts an ObjectStorage by a given field name.
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class SortObjectStorageViewHelper extends AbstractViewHelper
{
    /**
     * @return ObjectStorage|array
     */
    public function render(): ObjectStorage|array
    {
        $arguments = $this->arguments;
        return ObjectStorageUtility::sortBy(
            $arguments['storage'],
            $arguments['orderByField'],
            $arguments['mode'],
            $arguments['useStrCompare']);
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('storage', 'mixed', 'The Storage or Array to sort', true);
        $this->registerArgument('orderByField', 'string', 'The Parameter to sort by', false, 'uid');
        $this->registerArgument('mode', 'string', 'Order "default", "reverse" or "shuffle"', false, ObjectStorageUtility::MODE_DEFAULT);
        $this->registerArgument('useStrCompare', 'bool', 'use strcmp instead of strnatcasecmp', false, false);
    }
}
