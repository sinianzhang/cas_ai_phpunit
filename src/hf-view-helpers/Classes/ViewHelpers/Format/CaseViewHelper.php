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

use Hausformat\Lib\Utility\CaseTransformationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Formats the input to a given case.
 *
 * ## Examples
 *
 *     <hf:format.case value="hello world" case="camelCase" /> == "helloWorld"
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class CaseViewHelper extends AbstractViewHelper
{
    /**
     * @var string
     */
    protected const CASE_LOWER = 'lower';

    /**
     * Format the string with strtolower/strtoupper or GenaralUtility (the Camelcase Functions)
     * @return string
     */
    public function render(): string
    {
        $arguments = $this->arguments;
        $case = $arguments['case'];

        $value = $arguments['value'];
        if ($value === '') {
            $value = $this->renderChildren();
        }

        if($value === null) {
            $value = '';
        }

        return CaseTransformationUtility::transform($value, $case);
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('value', 'string', '', false, '');
        $this->registerArgument('case', 'string', 'lower | camelCase | upperCamelCase | underscored | upper', false, self::CASE_LOWER);
    }
}
