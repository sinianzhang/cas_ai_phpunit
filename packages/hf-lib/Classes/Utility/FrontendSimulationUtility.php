<?php

namespace Hausformat\Lib\Utility;

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

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\Request;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Routing\SiteRouteResult;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Aspect\PreviewAspect;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageInformationFactory;

/**
 * FrontendSimulationUtility
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class FrontendSimulationUtility
{
    /**
     * @var array|null
     */
    protected static $getBackup;

    /**
     * @var TypoScriptFrontendController|null
     */
    private static $tsfeBackup;

    /**
     * @var bool
     */
    protected static $resetTypo3Request = false;

    public static function resetFrontendEnvironment()
    {
        $GLOBALS['TSFE'] = self::$tsfeBackup;
        $_GET = self::$getBackup;

        if(self::$resetTypo3Request) {
            unset($GLOBALS['TYPO3_REQUEST']);
        }

        self::$tsfeBackup = null;
        self::$getBackup = null;
        self::$resetTypo3Request = false;

        \TYPO3\CMS\Extbase\Utility\FrontendSimulatorUtility::resetFrontendEnvironment();
    }

    /**
     * @param int $pageId
     * @param string $pageType
     * @param int $languageId
     * @param PageInformationFactory|null $pageInformationFactory
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \TYPO3\CMS\Core\Error\Http\StatusException
     * @throws \TYPO3\CMS\Frontend\Page\PageInformationCreationFailedException
     */
    public static function simulateFrontendEnvironment(int $pageId, string $pageType = '0', int $languageId = 0, ?PageInformationFactory $pageInformationFactory = null)
    {
        self::$getBackup = $_GET;
        $_GET = array_merge($_GET, ['L' => $languageId]);
        $context = GeneralUtility::getContainer()->get(Context::class);
        $site =  GeneralUtility::getContainer()->get(SiteFinder::class)->getSiteByPageId($pageId);
        $siteLanguage = $site->getLanguageById($languageId);
        $pageArguments = GeneralUtility::makeInstance(PageArguments::class, $pageId, $pageType, []);

        /** @var TypoScriptFrontendController $controller */
        $controller = GeneralUtility::makeInstance(
            TypoScriptFrontendController::class,
            $context,
            $site,
            $siteLanguage,
            $pageArguments,
            GeneralUtility::makeInstance(FrontendUserAuthentication::class)
        );

        self::$tsfeBackup = $GLOBALS['TSFE'] ?? null;

        $GLOBALS['TSFE'] = $controller;

        $serverRequest = null;
        $uriInterface = $site->getBase();
        $siteRoutResult = GeneralUtility::makeInstance(SiteRouteResult::class, $uriInterface, $site, $siteLanguage);

        if(isset($GLOBALS['TYPO3_REQUEST']) && !is_null($GLOBALS['TYPO3_REQUEST'])) {
            /** @var ServerRequest $serverRequest */
            $serverRequest = $GLOBALS['TYPO3_REQUEST'];
        } elseif(!isset($GLOBALS['TYPO3_REQUEST'])) {
            self::$resetTypo3Request = true;
            $_GET['id'] = $pageId;

            /** @var ServerRequest $serverRequest */
            $serverRequest = GeneralUtility::makeInstance(ServerRequest::class);
            $serverRequest = $serverRequest->withUri($uriInterface);
            $serverRequest = $serverRequest->withAttribute('site', $site);
            $serverRequest = $serverRequest->withAttribute('language', $siteLanguage);
            $serverRequest = $serverRequest->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE);
            $serverRequest = $serverRequest->withAttribute('frontend.controller', $controller);

            // public function __construct(UriInterface $uri, SiteInterface $site, ?SiteLanguage $language = null, string $tail = '', array $data = [])

            $pageFinalArguments = $site->getRouter()->matchRequest($serverRequest, $siteRoutResult);
            $serverRequest = $serverRequest->withAttribute('routing', $pageFinalArguments);

            $GLOBALS['TYPO3_REQUEST'] = $serverRequest;
        }

        if($pageInformationFactory == null) {
            /** @var \TYPO3\CMS\Frontend\Page\PageInformationFactory $pageInformationFactory */
            $pageInformationFactory = GeneralUtility::getContainer()->get(\TYPO3\CMS\Frontend\Page\PageInformationFactory::class);
        }

        // workaround: routing (pageId)
        $pageFinalArguments = $site->getRouter()->matchRequest($serverRequest, $siteRoutResult);
        $serverRequest = $serverRequest->withAttribute('routing', $pageFinalArguments);

        // workaround: context->isPreview=false
        $context->setAspect('frontend.preview', new PreviewAspect(false));

        $pageInformation = $pageInformationFactory->create($serverRequest);
        //$pageInformation->setId($pageId);

        $controller->initializePageRenderer($serverRequest);
        $controller->initializeLanguageService($serverRequest);
        $controller->id = $pageInformation->getId();
        $controller->page = $pageInformation->getPageRecord();
        $controller->contentPid = $pageInformation->getContentFromPid();
        $controller->rootLine = $pageInformation->getRootLine();
        $controller->config['rootLine'] = $pageInformation->getLocalRootLine();
    }

}
