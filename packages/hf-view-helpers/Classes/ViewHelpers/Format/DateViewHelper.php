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

use Hausformat\Lib\Domain\Exception\GenericException;
use Hausformat\Lib\Utility\DateUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Render a DateTime object according to the format given
 *
 * ## Examples
 *
 *     <hf:date.format format="d.m.Y H:i">{dateTimeObject}</hf:format.dateTime>
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class DateViewHelper extends AbstractViewHelper
{
    /**
     * @return string
     * @throws GenericException
     */
    public function render(): string
    {
        $arguments = $this->arguments;
        if ($arguments['date'] === null) {
            $date = $this->renderChildren();
            if ($date === null) {
                return '';
            }
        } else {
            $date = $arguments['date'];
        }
        if (!$date instanceof \DateTime) {
            try {
                $date = new \DateTime($date);
            } catch (\Exception $exception) {
                throw new GenericException('"' . $date . '" could not be parsed by DateTime constructor.', 1241722579);
            }
        }
        if ($arguments['strftime']) {
            return DateUtility::strftimeDateTime($arguments[ 'format' ], $date);
        }
        return $date->format($arguments['format']);
    }

    /**
     * Arguments initialization
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('date', 'mixed', 'either a DateTime object or a string that is accepted by DateTime constructor');
        $this->registerArgument('format', 'string', 'Format String which is taken to format the Date/Time', false, 'U');
        $this->registerArgument('strftime', 'bool', 'should strftime be used', false, false);
    }
}
