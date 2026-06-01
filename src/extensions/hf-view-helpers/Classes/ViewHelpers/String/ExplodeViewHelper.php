<?php declare(strict_types=1);

namespace Hausformat\ViewHelpers\ViewHelpers\String;

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
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Explodes a string by a seperator. Trims the result by default.
 *
 * ## Examples
 *
 *     <hf:string.explode string="a. b . .  c,d" seperator="." /> == ["a", "b", "c,d"]
 *
 *     <hf:string.explode string="a,  ,  c,d"  trim="0" removeEmptyValues="0" /> == ["a", " ", "   c", "d"]
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class ExplodeViewHelper extends AbstractViewHelper
{
    /**
     * @return array
     */
    public function render(): array
    {
        $arguments = $this->arguments;
        $string = $arguments['string'] ?? $this->renderChildren();

        if ($arguments['trim']) {
            return GeneralUtility::trimExplode(
                $arguments['separator'],
                $string,
                $arguments['removeEmptyValues']
            );
        }

        $explodedList = explode($arguments['separator'], $string);

        if ($arguments['removeEmptyValues']) {
            $explodedList = array_filter(
                $explodedList,
                function ($val) {
                    return !empty($val);
                }
            );
        }

        return $explodedList;
    }

    /**
     * Initialize arguments.
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('separator', 'string', 'The separator for explosion.', false, ',');
        $this->registerArgument('string', 'string', 'The string to explode', false, '');
        $this->registerArgument('trim', 'boolean', 'Trim items after explosion', false, true);
        $this->registerArgument('removeEmptyValues', 'boolean', 'Remove empty values after Explosion', false, true);
    }
}
