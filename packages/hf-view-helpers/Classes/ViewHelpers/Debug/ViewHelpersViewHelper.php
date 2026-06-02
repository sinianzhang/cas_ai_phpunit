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

use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Print out a VarDump of all included VH's
 *
 * ## Examples
 *
 *     <hf:debug.viewHelpers >
 *         <f:render section="someSection" />
 *         <!-- other fluid content -->
 *     </hf:debug.viewHelpers>
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class ViewHelpersViewHelper extends AbstractViewHelper
{
    /**
     * @var ViewHelperNode[]
     */
    protected $childViewHelperNodes = [];

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @return mixed
     * @throws \ReflectionException
     */
    public function render()
    {
        $nodes = [];

        foreach ($this->childViewHelperNodes as $viewHelperNode) {
            $viewHelper = $viewHelperNode->getUninitializedViewHelper();
            $arguments = $viewHelper->prepareArguments();
            $givenArguments = $viewHelperNode->getArguments();
            $viewHelperReflection = new \ReflectionClass($viewHelper);
            $viewHelperDescription = $viewHelperReflection->getDocComment();
            $viewHelperDescription = htmlentities($viewHelperDescription);
            $viewHelperDescription = '[CLASS DOC]' . LF . $viewHelperDescription . LF;
            $renderMethodDescription = $viewHelperReflection->getMethod('render')->getDocComment();
            $renderMethodDescription = htmlentities($renderMethodDescription);
            $renderMethodDescription = implode(LF, array_map('trim', explode(LF, $renderMethodDescription)));
            $renderMethodDescription = '[RENDER METHOD DOC]' . LF . $renderMethodDescription . LF;
            $output = '[RENDERED OUTPUT]' . LF . $viewHelperNode->evaluate($this->renderingContext) . LF;

            $argumentDefinitions = [];

            foreach ($arguments as &$argument) {
                if (method_exists($argument, 'getName')) {
                    $name = $argument->getName();
                    $argumentDefinitions[$name] = ObjectAccess::getGettableProperties($argument);
                }
            }
            $sections = [
                $viewHelperDescription,
                DebuggerUtility::var_dump($argumentDefinitions, '[ARGUMENTS]', 4, true, false, true),
                DebuggerUtility::var_dump($givenArguments, '[CURRENT ARGUMENTS]', 4, true, false, true),
                $renderMethodDescription,
                $output,
            ];

            array_push($nodes, implode(LF, $sections));
        }

        return '<pre>' . implode(LF . LF, $nodes) . '</pre>';
    }

    /**
     * Sets the direct child nodes of the current syntax tree node.
     *
     * @param \TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\AbstractNode[] $childNodes
     *
     * @return void
     */
    public function setChildNodes(array $childNodes)
    {
        foreach ($childNodes as $childNode) {
            if (true === $childNode instanceof ViewHelperNode) {
                array_push($this->childViewHelperNodes, $childNode);
            }
        }
    }
}
