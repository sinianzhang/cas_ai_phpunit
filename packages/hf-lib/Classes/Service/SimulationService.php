<?php

namespace Hausformat\Lib\Service;

use Psr\Http\Message\ServerRequestFactoryInterface;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;

class SimulationService implements SingletonInterface
{

    protected ?ConfigurationManager $configurationManager = null;
    protected ?SiteFinder $siteFinder = null;

    /**
     * @param int $rootPageIdForSettings
     *
     * @return mixed|\Psr\Http\Message\ServerRequestInterface
     * @throws \TYPO3\CMS\Core\Exception\SiteNotFoundException
     */
    public function getRealRequest($rootPageIdForSettings = 1) {
        $host = $_SERVER['HTTP_HOST'] ?? 'hausformat.com';
        $applicationType = $_SERVER['TYPO3_CONTEXT'] ?? 'Development';
        $requestFactory = GeneralUtility::makeInstance(ServerRequestFactoryInterface::class);
        if($GLOBALS['TYPO3_REQUEST']) {
            $request = $GLOBALS['TYPO3_REQUEST'];
        } else {
            $request = $requestFactory->createServerRequest('GET', 'https://' . $host);
        }

        $request = $request->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE);
        $GLOBALS['TYPO3_REQUEST'] = $request;
        $this->configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);

        // Schritt 2: SiteFinder nutzen, NACHDEM der Basis-Request existiert
        $this->siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $this->siteFinder->siteConfigurationChanged();
        $site = $this->siteFinder->getSiteByRootPageId($rootPageIdForSettings);

        // Schritt 3: Request mit Site-Informationen anreichern
        $request = $request
            ->withAttribute('site', $site)
            ->withAttribute('language', $site->getDefaultLanguage());

        $GLOBALS['TYPO3_REQUEST'] = $request;

        return $request;
    }

    /**
     * @param int $rootPageIdForSettings
     *
     * @return \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
     * @throws \TYPO3\CMS\Core\Exception\SiteNotFoundException     */
    public function getRealConfigurationManager(int $rootPageIdForSettings = 1): ConfigurationManager
    {
        $this->getRealRequest($rootPageIdForSettings);
        return $this->configurationManager;
    }

    /**
     * @param int $siteId
     *
     * @return \TYPO3\CMS\Core\Site\Entity\Site
     * @throws \TYPO3\CMS\Core\Exception\SiteNotFoundException
     */
    public function getRealSiteConfig(int $siteId = 1): Site
    {
        $this->getRealRequest($siteId);
        if ($this->configurationManager == null) {
            $this->configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        }
        $this->siteFinder->siteConfigurationChanged();
        return $this->siteFinder->getSiteByRootPageId($siteId);
    }

}