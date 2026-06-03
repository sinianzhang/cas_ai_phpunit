<?php
declare(strict_types=1);

namespace Hausformat\Lib\Context;

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

use TYPO3\CMS\Core\Context\AspectInterface;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Context\UserAspect;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * The Context class provides access to various aspects of the TYPO3 context, such as language, frontend user, and backend user.
 * It ensures safe retrieval of these aspects and handles exceptions if an aspect is not found.
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class Context
{
    private const ASPECT_LANGUAGE = 'language';

    private const ASPECT_FRONTEND_USER = 'frontend.user';

    private const ASPECT_BACKEND_USER = 'backend.user';

    /**
     * @var \TYPO3\CMS\Core\Context\Context
     */
    protected $context;

    public function __construct(?\TYPO3\CMS\Core\Context\Context $context = null)
    {
        $this->context = $context ?? GeneralUtility::makeInstance(\TYPO3\CMS\Core\Context\Context::class);
    }

    public function getLanguageAspect(): LanguageAspect
    {
        /** @var LanguageAspect $aspect */
        $aspect = $this->getAspect(self::ASPECT_LANGUAGE);
        return $aspect;
    }

    /**
     * @param string $name
     *
     * @return \TYPO3\CMS\Core\Context\AspectInterface
     */
    protected function getAspect(string $name): AspectInterface
    {
        try {
            return $this->context->getAspect($name);
        } catch (AspectNotFoundException $exception) {
            throw new \RuntimeException(
                sprintf(
                    'Aspect %s not found. This should not happen, since this is one of the default aspects.',
                    $name
                )
            );
        }
    }

    public function getFrontendUserAspect(): UserAspect
    {
        /** @var UserAspect $aspect */
        $aspect =  $this->getAspect(self::ASPECT_FRONTEND_USER);
        return $aspect;
    }

    public function getBackendUserAspect(): UserAspect
    {
        /** @var UserAspect $aspect */
        $aspect = $this->getAspect(self::ASPECT_BACKEND_USER);
        return $aspect;
    }
}
