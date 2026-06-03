<?php
declare(strict_types=1);

namespace Hausformat\Lib\Service;

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

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service class for handling language translations in TYPO3, providing utility methods to retrieve localized strings
 * based on the current request context (frontend or backend).
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class LanguageService
{
    /**
     * @param string $key
     *
     * @return null|string
     */
    public function sL(string $key)
    {
        if (($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
            && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend()) {
            $languageServiceFactory = GeneralUtility::makeInstance(LanguageServiceFactory::class);
            $siteLanguage = $GLOBALS['TYPO3_REQUEST']->getAttribute('language') ?? $GLOBALS['TYPO3_REQUEST']->getAttribute('site')->getDefaultLanguage();
            $ls = $languageServiceFactory->createFromSiteLanguage($siteLanguage);
            $value = $ls->sL($key);

            return $value !== '' ? $value : null;
        }
        if (is_object($GLOBALS['LANG'])) {
            $value = GlobalsService::getInstance()->getLanguageService()->sL($key);

            return $value !== '' ? $value : null;
        }

        return $key;
    }
}
