<?php declare(strict_types=1);

namespace Hausformat\ViewHelpers\ViewHelpers;

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

use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Calculate a mathematical expression. Optionally rounds the result.
 * Attention: The ViewHelper rounds the result by default!
 *
 * ## Example:
 *
 *     <hf:calc>2 * (5 + 5) - 5</hf:calc> // 15
 *     <hf:calc round="precise">1 / 3</hf:calc> // 0.3333...
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class CalcViewHelper extends AbstractViewHelper
{
    /** @var string */
    const ROUND_HALF = 'half';

    /** @var string */
    const ROUND_UP = 'up';

    /** @var string */
    const ROUND_DOWN = 'down';

    /** @var string */
    const ROUND_PRECISE = 'precise';

    /**
     * @return float|int
     * @throws Exception
     */
    public function render(): float | int
    {
        $arguments = $this->arguments;
        $value = $arguments['value'];
        $round = $arguments['round'] ?? self::ROUND_HALF;

        if (null === $value) {
            $value = $this->renderChildren();
        }

        // Return a Zero if the Value still is null.
        if ($value === null) {
            return 0;
        }

        if (str_contains($value, '{') && str_contains($value, '}')) {
            throw new \InvalidArgumentException('You have a typo in your expression: ' . $value);
        }

        $var = MathUtility::calculateWithParentheses($value);

        if (str_starts_with($var, 'ERROR:')) {
            throw new Exception($var . ' in "' . $value . '"');
        }

        $var = (float) $var;

        return match ($round) {
            self::ROUND_UP => ceil($var),
            self::ROUND_DOWN => floor($var),
            self::ROUND_PRECISE => $var,
            self::ROUND_HALF => round($var),
            default => throw new \InvalidArgumentException(
                sprintf(
                    'Invalid round mode "%s". Allowed values are: %s, %s, %s and %s.',
                    $round,
                    self::ROUND_HALF,
                    self::ROUND_UP,
                    self::ROUND_DOWN,
                    self::ROUND_PRECISE
                )
            ),
        };
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('value', 'string', '');
        $this->registerArgument('round', 'string', 'half | up | down | precise', false, self::ROUND_HALF);
    }
}
