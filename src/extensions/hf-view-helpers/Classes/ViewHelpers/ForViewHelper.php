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

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * For loop which can be configured how it behaves.
 *
 * ## Example:
 *
 *     <hf:for limit="10" increment="4" begin="2" variable="value" counter="index">
 *         {index}: {value}
 *     </hf:for>
 *     // == 1: 2, 2: 6, 2: 10
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class ForViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    /**
     * @return string
     */
    public function render(): string
    {
        $arguments = $this->arguments;
        $limit = $arguments['limit'];
        $begin = $arguments['begin'];
        $increment = $arguments['increment'];
        $variable = $arguments['variable'];
        $counter = $arguments['counter'];

        $variableProvider = $this->renderingContext->getVariableProvider();

        $content = '';
        $c = 1;

        for ($i = $begin; $i <= $limit; $i += $increment) {
            $variableProvider->add($variable, $i);
            $variableProvider->add($counter, $c);

            $content .= $this->renderChildren();

            $variableProvider->remove($variable);
            $variableProvider->remove($counter);

            $c++;
        }

        return $content;
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('limit', 'int', 'End the loop once the value is reached', true);
        $this->registerArgument('begin', 'int', 'Value to start with', false, 0);
        $this->registerArgument('increment', 'int', 'Increment by this value', false, 1);
        $this->registerArgument('variable', 'string', 'The current value of the loop', false, 'i');
        $this->registerArgument('counter', 'string', 'The current index of the loop', false, 'c');
    }
}
