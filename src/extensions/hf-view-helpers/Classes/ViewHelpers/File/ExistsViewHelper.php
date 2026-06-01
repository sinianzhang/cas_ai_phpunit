<?php

namespace Hausformat\ViewHelpers\ViewHelpers\File;

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

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * Check if either a file or a folder exists
 *
 * ## Examples
 *
 *     <hf:file.exists file="robots.txt">
 *         <f:then>
 *             file exists
 *         </f:then>
 *     </hf:file.exists>
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class ExistsViewHelper extends AbstractConditionViewHelper
{
    /**
     * This method decides if the condition is TRUE or FALSE. It can be overriden in extending viewhelpers to adjust functionality.
     *
     * @param array $arguments ViewHelper arguments to evaluate the condition for this ViewHelper, allows for flexiblity in overriding this method.
     * @param RenderingContextInterface|null $renderingContext
     *
     * @return bool
     */
    public static function verdict(array $arguments, ?RenderingContextInterface $renderingContext = null): bool
    {
        if (isset($arguments['file'])) {
            $file = GeneralUtility::getFileAbsFileName($arguments['file']);

            if($file == '') {
                $file = GeneralUtility::getFileAbsFileName(trim($arguments['file'], '/'));
            }

            return (file_exists($file) || file_exists(Environment::getPublicPath() . '/' . $file)) && is_file($file);
        }

        if (isset($arguments['directory'])) {
            $directory = $arguments['directory'];

            return (is_dir($directory) || is_dir(Environment::getPublicPath() . '/' . $directory));
        }

        return false;
    }

    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('file', 'string', 'Filename which must exist to trigger f:then rendering', false);
        $this->registerArgument('directory', 'string', 'Directory which must exist to trigger f:then rendering', false);
    }
}
