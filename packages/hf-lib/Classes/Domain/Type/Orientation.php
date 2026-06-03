<?php

namespace Hausformat\Lib\Domain\Type;

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

/**
 * An enumeration/Type for Page-Orientation
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
enum Orientation: string
{
    case LANDSCAPE = 'landscape';

    case PORTRAIT = 'portrait';

    case SQUARE = 'square';

    public function isLandscape(): bool
    {
        return $this === self::LANDSCAPE;
    }

    public function isPortrait(): bool
    {
        return $this === self::PORTRAIT;
    }

    public function isSquare(): bool
    {
        return $this === self::SQUARE;
    }
}
