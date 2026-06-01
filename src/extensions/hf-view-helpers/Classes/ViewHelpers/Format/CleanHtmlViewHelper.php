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
 * Removes <p> tags with &nbsp; inside
 * 
 * @author .hausformat <entwicklung@hausformat.com>
 */
class CleanHtmlViewHelper extends AbstractViewHelper
{

    protected $escapeChildren = false;
    protected $escapeOutput = false;

    /**
     * @return string
     */
    public function render()
    {

        $value = $this->arguments['value'] ?? $this->renderChildren();

        // Removes <p> tags with &nbsp; inside
        return preg_replace('/<p[^>]*?>(&nbsp;|[\s])*<\/p>/uiUs', '', $value);
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('value', 'string', '');
    }
}
