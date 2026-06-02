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
 * Encodes data as JSON.
 *
 * ## Examples
 *
 *     <hf:format.jsonEncode options="JSON_HEX_TAG">{0: 'eins', 1: 'zwei'}</hf:format.jsonEncode>
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class JsonEncodeViewHelper extends AbstractViewHelper
{
    /**
     * Uses json_encode on the passed value
     *
     * @return string
     * @throws \Exception
     */
    public function render(): string
    {
        $arguments = $this->arguments;
        $options = 0;
        if ($arguments['options']) {
            $options = $arguments['options'];
        } else {
            if ($arguments['pretty']) {
                $options |= JSON_PRETTY_PRINT;
            }
            if ($arguments['forceObject']) {
                $options |= JSON_FORCE_OBJECT;
            }
        }

        if (!$arguments['value']) {
            $value = $this->renderChildren();
        } else {
            $value = $arguments['value'];
        }

        return json_encode($value, $options);
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('value', 'mixed', 'The value being encoded. Works with UTF-8 encoded data only', false);

        $this->registerArgument(
            'options',
            'int',
            'Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_FORCE_OBJECT. Defaults to 0.',
            false,
            0
        );

        $this->registerArgument(
            'pretty',
            'bool',
            'Make a pretty JSON for Output. Would be overwritten by \'options\'. Default: false',
            false,
            false
        );

        $this->registerArgument(
            'forceObject',
            'bool',
            'Force output as Objects. Would be overwritten by \'options\'. Default: false',
            false,
            false
        );
    }
}
