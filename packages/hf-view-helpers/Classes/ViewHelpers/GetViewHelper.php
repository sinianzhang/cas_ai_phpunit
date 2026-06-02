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
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Get a nested value from an object by path
 *
 * ## Example:
 *
 *     <f:for each="{hf:get(obj: '{mainnavigation}', property: 'title', multipleAsArray: '1')}" as="title">
 *         {title} <br>
 *     </f:for> // lists {mainnavigation.*.title}
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class GetViewHelper extends AbstractViewHelper
{
    /**
     * @return mixed|null
     */
    public function render(): mixed
    {
        return ObjectStorageUtility::getValueFromDottedPath(
            $this->arguments['property'],
            $this->arguments['obj'],
            $this->arguments['multipleAsArray']
        );
    }

    /**
     * initialize Arguments
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('obj', 'mixed', 'The Object/Array to find the property in it.', true);
        $this->registerArgument('property', 'string', 'Property to get out of the given object', true);
        $this->registerArgument('multipleAsArray', 'boolean', 'Returns multiple values if multiple were found', false, false);
    }
}
