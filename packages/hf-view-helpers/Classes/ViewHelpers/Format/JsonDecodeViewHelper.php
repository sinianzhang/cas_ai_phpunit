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

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Decodes a JSON string.
 *
 * ## Examples
 *
 *     <hf:format.jsonDecode json="[{1:"VALUENAME}]" assoc="false" depth="10"/>
 *
 *     <hf:format.jsonDecode assoc="false" depth="10">
 *         [{1:"VALUENAME"}]
 *     </hf:format.jsonDecode>
 *
 *     {hf:format.jsonDecode(json:'[{1:\'VALUENAME\'}]',assoc:'false',depth:'10')}
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class JsonDecodeViewHelper extends AbstractViewHelper
{
    /**
     * @return mixed
     * @throws \Exception
     */
    public function render(): mixed
    {
        $arguments = $this->arguments;
        if ($arguments['json'] === null) {
            $json = $this->renderChildren();
        } else {
            $json = $arguments['json'];
        }

        if (empty($json)) {
            return null;
        }

        $value = json_decode($json, $arguments['assoc'], $arguments['depth']);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('The provided argument is invalid JSON.', 1451124524);
        }

        return $value;
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('json', 'string', 'The json string being decoded', false);

        $this->registerArgument(
            'assoc',
            'bool',
            'When true, returned objects will be converted into associative arrays',
            false,
            false
        );

        $this->registerArgument('depth', 'int', 'User specified nesting limit', false, 512);
    }
}
