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

use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Formats a Flexform to an Array
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class FlexFormToArrayViewHelper extends AbstractViewHelper
{
    /**
     * @return void
     */
    public function render(): void
    {
        /** @var FlexFormTools $flexFormTools */
        $flexFormTools = GeneralUtility::makeInstance(FlexFormTools::class);

        $arguments = $this->arguments;

        if (!isset($arguments['as'])) {
            $arguments['as'] = 'newArray';
        }

        $array = $flexFormTools->convertFlexFormContentToArray($arguments['value']);
        $container = $this->renderingContext->getVariableProvider();

        if (!$container->exists($arguments['as'])) {
            $container->add($arguments['as'], $array);
        }
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('value', 'string', 'Flexform', true);
        $this->registerArgument('as', 'string', 'Output variable name', false, 'newArray');
    }
}
