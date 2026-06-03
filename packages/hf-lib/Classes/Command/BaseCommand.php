<?php
declare(strict_types=1);

namespace Hausformat\Lib\Command;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * Base class that should be extended when creating new Symfony command classes.
 * Final version for TYPO3 v12/v13, handling CLI context for Extbase ConfigurationManager.
 */
abstract class BaseCommand extends Command
{
    /**
     * The root page ID of the site configuration to use for loading TypoScript settings.
     * Can be overridden by child commands if necessary.
     */
    protected int $rootPageIdForSettings = 1;

    protected bool $moduleSettings = false;
    protected array $settings = [];
    protected SymfonyStyle $inputOutput;
    protected string $extensionKey;

    protected ConfigurationManager $configurationManager;
    protected PersistenceManager $persistenceManager;
    protected SiteFinder $siteFinder;
    protected LogManager $logManager;

    public function __construct(
    ) {
        parent::__construct();
        $this->setExtensionKey();
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->inputOutput = new SymfonyStyle($input, $output);
        $this->logManager = GeneralUtility::makeInstance(LogManager::class);

        // Nur fortfahren, wenn noch kein Request existiert (echter CLI-Call)
        if (isset($GLOBALS['TYPO3_REQUEST']) && $GLOBALS['TYPO3_REQUEST'] instanceof ServerRequestInterface) {
            $this->configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
            $this->persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
            $this->siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        } else {
            try {
                // Schritt 1: Host aus der Umgebung holen (dein Vorschlag!)
                $host = $_SERVER['HTTP_HOST'] ?? 'hausformat.com';
                $applicationType = $_SERVER['TYPO3_CONTEXT'] ?? 'Development';
                $requestFactory = GeneralUtility::makeInstance(ServerRequestFactoryInterface::class);
                $request = $requestFactory->createServerRequest('GET', 'https://' . $host);

                $request = $request->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE);
                $GLOBALS['TYPO3_REQUEST'] = $request;
                $this->configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);

                // Schritt 2: SiteFinder nutzen, NACHDEM der Basis-Request existiert
                $this->siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
                $this->siteFinder->siteConfigurationChanged();
                $site = $this->siteFinder->getSiteByRootPageId($this->rootPageIdForSettings);

                // Schritt 3: Request mit Site-Informationen anreichern
                $request = $request
                    ->withAttribute('site', $site)
                    ->withAttribute('language', $site->getDefaultLanguage());

                $GLOBALS['TYPO3_REQUEST'] = $request;

                // Schritt 4: Jetzt die Services holen, die den Request brauchen
                $this->configurationManager->setRequest($GLOBALS['TYPO3_REQUEST']);
                $this->persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);

            } catch (\Exception $e) {
                $this->getLogger()->critical('Could not initialize CLI context: ' . $e->getMessage(), ['exception' => $e]);
                return; // Abbruch, da keine Settings geladen werden können
            }
        }

        $this->initializeSettings();
    }

    protected function setExtensionKey(): void
    {
        if (isset($this->extensionKey)) {
            return;
        }
        $className = static::class;
        $this->extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored(
            GeneralUtility::trimExplode('\\', $className)[1] ?? ''
        );
    }

    private function initializeSettings(): void
    {
        try {
            //$this->configurationManager->clearConfigurationCache(); // Sicherstellen, dass nichts Falsches gecached ist
            $this->settings = $this->configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
                $this->extensionKey
            );
        } catch (\Exception $e) {
            $this->settings = [];
            $this->getLogger()->error(
                'Could not load extension settings for "' . $this->extensionKey . '". This might happen if no request context could be built.',
                ['exception' => $e]
            );
        }
    }

    public function persistAll(): void
    {
        $this->persistenceManager->persistAll();
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logManager->getLogger(static::class);
    }

    /** @throws SiteNotFoundException */
    protected function getSiteConfiguration(int $pid = 1): Site
    {
        return $this->siteFinder->getSiteByPageId($pid);
    }

    /** @throws SiteNotFoundException */
    protected function getSiteLanguageByUid(int $languageUid, int $pid = 1): ?SiteLanguage
    {
        $siteConfig = $this->getSiteConfiguration($pid);
        foreach ($siteConfig->getAllLanguages() as $language) {
            if ($language->getLanguageId() === $languageUid) {
                return $language;
            }
        }
        return null;
    }
}
