<?php declare(strict_types=1);

namespace Hausformat\ViewHelpers\ViewHelpers;

use TYPO3\CMS\Core\Exception;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * This ViewHelper is designed for components. It is basically a variable and or VH together on steroids.
 * By default, a variable is required.
 *
 * ## Example:
 *
 *     <hf:arg name="classToToggle" type="string" /> // if value is not set, it will throw an error
 *
 * @group hf-viewhelpers
 * @author .hausformat <entwicklung@hausformat.com>
 */
class ArgViewHelper extends AbstractViewHelper
{
    const VALID_TYPES = ['string', 'int', 'bool', 'float', 'array', 'object', 'callable'];

    /**
     * @return void
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('name', 'string', 'Name of the argument', true);
        $this->registerArgument('description', 'string', 'This has no effect, purely for documentation purposes');
        $this->registerArgument('default', 'string', 'Default value');
        $this->registerArgument('optional', 'bool', 'If set, the argument is optional', false, false);
        $this->registerArgument('type', 'string', 'Type of the argument (string, int, bool, float, array, object, callable)');
    }

    /**
     * @throws \TYPO3\CMS\Core\Exception
     */
    public function render(): void
    {
        $arguments = $this->arguments;
        $renderingContext = $this->renderingContext;
        $name = $arguments[ 'name' ];
        $default = $arguments[ 'default' ];
        $optional = $arguments[ 'optional' ];
        $type = $arguments[ 'type' ];

        $value = $renderingContext->getVariableProvider()->get($name);

        if (is_null($value) && !is_null($default)) {
            $value = $default;
            $renderingContext->getVariableProvider()->add($name, $value);
        }
        if (is_null($value) && !$optional) {
            throw new Exception(
                'Argument Exception: Argument ' . $name . ' is not set, is not optional and no default value is set',
                1471715915);
        }
        if (!is_null($type) && !is_null($value)) {
            if (!in_array($type, self::VALID_TYPES)) {
                throw new Exception(
                    'Argument Exception: Invalid type ' . $type . ' for argument ' . $name . '. Valid types are: ' . implode(', ', self::VALID_TYPES),
                    1471715915);
            }

            $invalidType = match ($type) {
                'string' => !is_string($value),
                'int' => !(is_numeric($value) && (string)$value === (string)(int)$value),
                'bool' => !is_bool($value) && !in_array(strtolower((string)$value), ['1', '0', 'true', 'false'], true),
                'float' => !(is_numeric($value) && (string)$value === (string)(float)$value),
                'array' => !is_array($value),
                'object' => !is_object($value),
                'callable' => !is_callable($value),
                default => true,
            };
            if ($invalidType) {
                throw new Exception(
                    'Argument Exception: Variable ' . $name . '(' . $value . ') is not of type ' . $type . ', received type ' . gettype($value),
                    1471715915);
            }
        }
    }
}
