<?php

namespace Hausformat\Lib\Service\Interfaces;

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
 * TypoScript Service Interface
 *
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
interface TypoScriptServiceInterface
{
    /**
     * get the typoscript settings
     *
     * @param string $subset optional Subset of the typoscript array
     *
     * @return mixed
     */
    public function getFrameworkConfig($subset = null);

    /**
     * get a specific typoscript key from the settings section
     *
     * @param string $key the settings key
     *
     * @return mixed
     */
    public function getSetting($key);
}
