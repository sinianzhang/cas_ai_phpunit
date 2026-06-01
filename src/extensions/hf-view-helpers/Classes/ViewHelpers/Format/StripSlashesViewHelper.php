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
 * Strips slashes from a string
 *
 * ## Examples
 *
 *     <hf:format.stripslashes value="hello \'world\'!" /> == "hello 'world'!"
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class StripSlashesViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeChildren = false;

    /**
     * @return string
     * @throws \Exception
     */
    public function render(): string
    {
        $value = $this->arguments['value'];

        if ($value == '') {
            $value = $this->renderChildren();
        }

        return stripslashes($value);
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('value', 'string', '');
    }
}
