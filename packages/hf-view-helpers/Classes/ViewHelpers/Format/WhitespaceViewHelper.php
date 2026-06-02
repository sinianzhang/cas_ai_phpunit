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
 * Strips multiple whitespaces and trims a string.
 *
 * ## Examples
 *
 *     <f:variable name="myVar" value="hello       world  !    " />
 *     <hf:format.whitespace trim="1">{myVar}</hf:format.whitespace> == "hello world !"
 *     <hf:format.whitespace trim="0">{myVar}</hf:format.whitespace> == "hello world ! "
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class WhitespaceViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeChildren = false;

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @return string the resulting string which is directly shown
     */
    public function render(): string
    {
        $body = $this->arguments['value'] ?? $this->renderChildren();

        $body = preg_replace('/[\s]+/', ' ', $body);

        if (isset($this->arguments['trim']) && !$this->arguments['trim']) {
            return $body;
        }

        return trim($body);
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('value', 'string', '');
        $this->registerArgument('trim', 'bool', '', false, true);
    }
}
