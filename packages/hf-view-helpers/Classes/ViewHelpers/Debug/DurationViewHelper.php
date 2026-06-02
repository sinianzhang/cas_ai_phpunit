<?php

namespace Hausformat\ViewHelpers\ViewHelpers\Debug;

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

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Measure the time it takes to render a part of the Fluid-Template
 *
 * ## Examples
 *
 *     <hf:debug.duration title="myTitle">
 *         <f:render section="mySection" />
 *     </hf:debug.duration>
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class DurationViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('name', 'string', 'optional custom title for the debug output');
        $this->registerArgument('title', 'string', 'optional custom title for the debug output');
        $this->registerArgument(
            'plainText',
            'bool',
            'If TRUE, the dump is in plain text, if FALSE the debug output is in HTML format.',
            false,
            false
        );
        $this->registerArgument(
            'ansiColors',
            'bool',
            'If TRUE, ANSI color codes is added to the plaintext output, if FALSE (default) the plaintext debug output not colored.',
            false,
            false
        );
        $this->registerArgument(
            'inline',
            'bool',
            'if TRUE, the dump is rendered at the position of the <f:debug> tag. If FALSE (default), the dump is displayed at the top of the page.',
            false,
            false
        );
    }

    /**
     * Count the Milliseconds for to Render a Part of the Fluid-Template
     *
     * @return string the calculated result
     */
    public function render(): string
    {
        $this->arguments['title'] = $this->arguments['name'] && !$this->arguments['title'] ? $this->arguments['name'] : $this->arguments['title'];
        $label = $this->arguments['title'] ?: 'Milliseconds';

        $start = round(microtime(true) * 1000);
        $content = $this->renderChildren();
        $end = round(microtime(true) * 1000);

        return DebuggerUtility::var_dump(
                $end - $start,
                $label,
                8,
                (bool)$this->arguments['plainText'],
                (bool)$this->arguments['ansiColors'],
                (bool)$this->arguments['inline']
            ) . $content;
    }
}

