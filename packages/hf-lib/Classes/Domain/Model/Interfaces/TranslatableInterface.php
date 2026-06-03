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
 * Interface for all translatable model classes
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
interface TranslatableInterface
{
    /**
     * getter for l10nParent
     *
     * @return int
     */
    public function getL10nParent();

    /**
     * getter for languageUid
     *
     * @return int
     */
    public function getLanguageUid();

    /**
     * getter for localizedUid
     *
     * @return int
     */
    public function getLocalizedUid();

    /**
     * setter for l10nParent
     *
     * @param int $value
     */
    public function setL10nParent($value);

    /**
     * setter for languageUid
     *
     * @param int $value
     */
    public function setLanguageUid($value);

    /**
     * setter for localizedUid
     *
     * @param int $value
     */
    public function setLocalizedUid($value);
}
