<?php declare(strict_types=1);

namespace Hausformat\ViewHelpers\ViewHelpers\Variable;

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
 * Removes a variable from the scope.
 *
 * ## Examples
 *
 *     <f:variable name="myVariable" value="someValue" />
 *     <hf:variable.remove name="myVariable" />
 *     <f:debug>{myVariable}</f:debug>
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class RemoveViewHelper extends AbstractViewHelper
{
    /**
     * @return void
     */
    public function render(): void
    {
        $arguments = $this->arguments;

        if (!isset($arguments['name'])) {
            throw new \InvalidArgumentException('argument \'name\' is required');
        }

        $name = $arguments['name'];
        $container = $this->renderingContext->getVariableProvider();

        if ($container->exists($name)) {
            $container->remove($name);
        }
    }

    /**
     * Initialize arguments.
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('name', 'string', 'The Name of the Variable to remove ', true);
    }
}
