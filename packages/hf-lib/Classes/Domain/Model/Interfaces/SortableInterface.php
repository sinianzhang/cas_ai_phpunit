<?php

namespace Hausformat\Lib\Domain\Model\Interfaces;

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
 * Interface for all sortable model classes
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
interface SortableInterface
{
    /**
     * getter for sorting
     *
     * @return int
     */
    public function getSorting();

    /**
     * setter for sorting
     *
     * @param int $value
     */
    public function setSorting($value);
}
