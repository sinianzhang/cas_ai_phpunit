<?php

declare(strict_types=1);

namespace B13\SiteT3demo;

/*
 * This file is part of TYPO3 CMS-extension site_t3demo by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Serverreset
{
    /**
     * Return the upcoming server reset time, or an empty string if no reset is set.
     *
     * @param  string $content   Empty string (no content to process)
     * @param  array  $conf      TypoScript configuration
     * @return string            timestamp or 0
     */
    public function nextServerReset(string $content, array $conf): string
    {
        $resetTime = [
            'resetTime' => (int)GeneralUtility::makeInstance(Registry::class)->get('tx_typo3_demo', 'next_content_sync', '0'),
        ];
        return json_encode($resetTime, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES);
    }
}
