<?php declare(strict_types=1);

namespace Hausformat\ViewHelpers\ViewHelpers\Format;

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

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Rounds a number to a given precision, rounding type and number format.
 * The rounding type can be "upToHalf", "downToHalf", "half", "up" or "round".
 *
 * ## Example:
 *
 *     <hf:round value="12.3456789" roundType="upToHalf" decimals="2" /> == 12.35
 *     <hf:round value="12.3456789" roundType="half" decimals="3" /> == 12.346
 *     <hf:round value="12.3456789" roundType="down" /> == 12
 *     <hf:round value="1234567.89" thousandsSeparator="'" /> == 1'234'568
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class RoundViewHelper extends AbstractViewHelper
{
    /**
     * @return string
     */
    public function render(): string
    {
        $arguments = $this->arguments;

        $value = $arguments['value'];
        $removeTextParts = $arguments['removeTextParts'];
        $divider = $arguments['divider'];
        $numberFormat = $arguments['numberFormat'];
        $roundType = $arguments['roundType'];
        $decimals = $arguments['decimals'];
        $thousandsSeparator = $arguments['thousandsSeparator'];
        $decimalPoint = $arguments['decimalPoint'];

        if (null === $value) {
            $value = $this->renderChildren();
        }

        if ($removeTextParts) {
            $value = preg_replace("/[a-zA-Z'`,]/u", "", (string) $value);
        }

        $value = (float) $value;

        if ($divider != 0) {
            $value = $value / $divider;
        }

        if ($decimals != 0 || $roundType == 'round') {
            $returnValue = (round($value, $decimals));
        } elseif ($roundType == 'up-to-half' || $roundType == 'upToHalf') {
            $returnValue = (ceil($value * 2) / 2);
        } elseif ($roundType == 'down-to-half' || $roundType == 'downToHalf') {
            $returnValue = (floor($value * 2) / 2);
        } elseif ($roundType == 'half') {
            $returnValue = (round($value * 2, $decimals) / 2);
        } elseif ($roundType == 'up') {
            $returnValue = (ceil($value));
        } else {
            $returnValue = (floor($value));
        }

        if ($thousandsSeparator != '' || $decimalPoint != '') {
            if ($decimalPoint == '') {
                $decimalPoint = '.';
            }
            $returnValue = number_format($returnValue, $decimals, $decimalPoint, $thousandsSeparator);
        }

        $returnValue .= $numberFormat;

        return $returnValue;
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('value', 'mixed', '');
        $this->registerArgument('divider', 'int', '', false, 0);
        $this->registerArgument('numberFormat', 'string', '', false, '');
        $this->registerArgument('removeTextParts', 'bool', '', false, true);
        $this->registerArgument('roundType', 'string', 'upToHalf | downToHalf | half | up | round', false, 'up-to-half');
        $this->registerArgument('decimals', 'int', '', false, 0);
        $this->registerArgument('thousandsSeparator', 'string', '', false, '');
        $this->registerArgument('decimalPoint', 'string', 'Default is a dot', false, '');
    }
}
