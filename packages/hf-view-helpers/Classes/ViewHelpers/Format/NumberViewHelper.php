<?php

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
 * Formats a number based on the given arguments.
 * TODO: There are some weird behaviours, this should be tested thoroughly.
 *
 * ## Example:
 *
 *     <hf:numberFormat format="###,###.00" prefix="$" suffix=" USD" returnZeroAsEmptyString="true">
 *         1234567890
 *     </hf:numberFormat> == $1,234,567,890.00 USD
 *     <hf:numberFormat returnZeroAsEmptyString="1" zeroEmptyString="no value :(" /> == no value :(
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class NumberViewHelper extends AbstractViewHelper
{
    /**
     * @return mixed|string
     */
    public function render(): mixed
    {
        $arguments = $this->arguments;
        $format = $arguments['format'];
        $prefix = $arguments['prefix'];
        $suffix = $arguments['suffix'];
        $fillInt = $arguments['fillInt'];
        $fillFloat = $arguments['fillFloat'];
        $returnZeroAsEmptyString = $arguments['returnZeroAsEmptyString'];
        $zeroEmptyString = $arguments['zeroEmptyString'];
        $dontShowMinusSign = $arguments['dontShowMinusSign'];
        $factor = $arguments['factor'];

        $amount = $arguments['value'] ?? $this->renderChildren();
        if (!is_numeric($amount)) {
            return $amount;
        }
        $amount *= $factor;

        if (0 == $amount && $returnZeroAsEmptyString) {
            return $zeroEmptyString;
        }

        // format must at least be one char long
        if (empty($format)) {
            $format = 0;
        }

        // if no decimal point is provided, we add it at the end for the regex to match
        if (!str_contains($format, '.')) {
            $format = $format . '.';
        }

        $pattern = '@^([^#0]?)(?:[#0]*)([^#0]?)(?:[#0]*)([^#0])(0*)(#*)$@si';
        preg_match($pattern, $format, $separator);
        $count['int'] = [
            'zerofill' => substr_count($format, '0', 0, strrpos($format, $separator[3])),
        ];
        $count['float'] = [
            'zerofill' => strlen($separator[4]),
            'maxlength' => strlen($separator[4]) + strlen($separator[5]),
        ];

        // if integer has no #, set the maxlength
        if (0 === preg_match('?(#+)?', substr($format, 0, strrpos($format, $separator[3])))) {
            $count['int']['maxlength'] = substr_count(substr($format, 0, strrpos($format, $separator[3])), '0');
        }

        // get integer
        $integer = (int)abs($amount);
        $count['int']['length'] = strlen((string)$integer);

        // shorten
        if (isset($count['int']['maxlength']) && $count['int']['length'] > $count['int']['maxlength']) {
            $integer = number_format((float)substr((string)$integer, -1 * $count['int']['maxlength']), 0, $separator[3], $separator[2]);
            $count['int']['length'] = strlen($integer);
        }

        // zerofill
        if ($count['int']['length'] < $count['int']['zerofill']) {
            $difference = $count['int']['zerofill'] - $count['int']['length'];
            $integer = strrev($integer);
            $integer .= str_repeat($fillInt, $difference);
            $integer = str_split($integer, 3);
            $integer = implode($separator[2], $integer);
            $integer = strrev($integer);
        } else {
            $integer = number_format($integer, 0, $separator[3], $separator[2]);
        }

        // get float
        if (0 != $amount - (int)$amount) {
            $float = substr((string)$amount, strrpos((string)$amount, '.') + 1);
            $float = strrev(strrev($float));
        } else {
            $float = 0;
        }

        $count['float']['length'] = strlen($float);
        // zerofill float if needed
        if ($count['float']['zerofill'] > $count['float']['length']) {
            $float .= str_repeat($fillFloat, $count['float']['zerofill'] - $count['float']['length']);
        } // strip float if needed
        elseif ($count['float']['maxlength'] < $count['float']['length']) {
            $float = substr($float, 0, $count['float']['maxlength']);
        }

        // string int and float together
        if (0 < strlen($separator[4])
            || (0 < strlen($separator[5]) && $float)
        ) {
            $return = $integer . $separator[3] . $float;
        } else {
            $return = $integer;
        }

        // prepend - if negative
        if ($amount < 0 && !$dontShowMinusSign) {
            if (empty($separator[1])) {
                $return = '-' . $return;
            } else {
                $return = $separator[1] . $return;
            }
        }

        return $prefix . $return . $suffix;

    }

    public function initializeArguments(): void
    {
        $this->registerArgument('value', 'string', '');
        $this->registerArgument('format', 'string', '');
        $this->registerArgument('prefix', 'string', '', false, '');
        $this->registerArgument('suffix', 'string', '', false, '');
        $this->registerArgument('fillInt', 'string', '', false, '0');
        $this->registerArgument('fillFloat', 'string', '', false, '0');
        $this->registerArgument('returnZeroAsEmptyString', 'bool', '', false, false);
        $this->registerArgument('zeroEmptyString', 'string',
                                'String to return if the value is 0 and returnZeroAsEmptyString is true', false, '');
        $this->registerArgument('dontShowMinusSign', 'bool', '', false, false);
        $this->registerArgument('factor', 'int', 'Multiplies the value with this factor', false, 1);
    }
}
