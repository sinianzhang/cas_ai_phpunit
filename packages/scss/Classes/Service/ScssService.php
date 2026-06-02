<?php declare(strict_types=1);

namespace Hausformat\Scss\Service;

use Hausformat\Scss\Compiler;
use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception;
use TYPO3\CMS\Core\Cache\Exception\InvalidDataException;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * SCSS service
 */
class ScssService implements SingletonInterface
{

    /**
     * @var ContentObjectRenderer $contentObjectRenderer
     */
    protected ContentObjectRenderer $contentObjectRenderer;

    /**
     * @var ConfigurationManager $configurationManager
     */
    protected ConfigurationManager $configurationManager;

    protected array $variables = [];
    protected bool $variablesLoaded = false;
    protected string $libFiles = '';
    protected bool $libFilesLoaded = false;

    public function __construct(
        ConfigurationManager $configurationManager,
        ContentObjectRenderer $contentObjectRenderer
    ) {
        $this->configurationManager = $configurationManager;
        $this->contentObjectRenderer = $contentObjectRenderer;
    }

    /**
     * Get all lib files
     *
     * @return string
     */
    public function getLibImport(): string
    {
        if ($this->libFilesLoaded) {
            return $this->libFiles;
        }
        $this->libFilesLoaded = true;

        $cssFiles = $this->getIncludedCSSFiles();

        $libFiles = [];
        $topLibFiles = [];
        // Set file settings
        foreach ($cssFiles as $fileConfig) {
            $subConf = $fileConfig['tagAttributes'] ?? [];
            if(isset($subConf[ 'isLib' ]) && $subConf[ 'isLib' ] == "1") {
                if (isset($subConf['order'])) {
                    $topLibFiles[$subConf['order']] = $fileConfig['file'];
                } else {
                    $libFiles[] = $fileConfig['file'];
                }
            }
        }

        // sort lib files
        ksort($topLibFiles);
        // reverse to get the right order
        $topLibFiles = array_reverse($topLibFiles);
        $libFiles = array_merge($topLibFiles, $libFiles);

        $libImport = '';
        $absLibFiles = [];
        foreach ($libFiles as $libFile) {
            $path = GeneralUtility::getFileAbsFileName($libFile);
            $absLibFiles[] = $path;
            $libImport .= "@import '" . $path ."';";
        }
        $this->checkCache($absLibFiles);
        $this->libFiles = $libImport;
        return $libImport;
    }

    /**
     * Dev only, clears scss cache if lib files change
     *
     * @param array $libFiles
     */
    private function checkCache(array $libFiles): void
    {
        if (!Environment::getContext()->isDevelopment()) {
            return;
        }

        try {
            /** @var FileBackend $cache */
            $cache = GeneralUtility::makeInstance(CacheManager::class)
                                   ->getCache(Compiler::CACHE_KEY);
        } catch (NoSuchCacheException $e) {
            return;
        }

        $calculatedContentHash = '';
        $cacheKey = hash('sha1', implode(',', $libFiles));
        foreach ($libFiles as $libFile) {
            $calculatedContentHash .= md5(file_get_contents($libFile));
        }

        if ($cache->has($cacheKey)) {
            $contentHashCache = $cache->get($cacheKey);
            if ($contentHashCache === $calculatedContentHash) {
                return;
            }
            $cache->flushByTag(Compiler::CACHE_KEY);
        }
        try {
            $cache->set($cacheKey, $calculatedContentHash, [Compiler::CACHE_KEY], 0);
        } catch (InvalidDataException|Exception $e) {
            return;
        }
    }

    /**
     * @return array
     */
    public function getVariables(): array
    {
        if ($this->variablesLoaded) {
            return $this->variables;
        }
        $this->variablesLoaded = true;

        $setup = $this->getTypoScriptSetup();
        $variables = [];
        if (\is_array($setup['plugin.']['tx_scss.']['variables.'] ?? null)) {
            $variables = $setup[ 'plugin.' ][ 'tx_scss.' ][ 'variables.' ];
        }
        if (count($variables) === 0) {
            return [];
        }
        $parsedTypoScriptVariables = [];
        foreach ($variables as $variable => $key) {
            if (array_key_exists($variable . '.', $variables)) {
                $content = $this->contentObjectRenderer->cObjGetSingle(
                    $variables[$variable],
                    $variables[$variable . '.']);
                $parsedTypoScriptVariables[$variable] = $content;

            } elseif (!str_ends_with($variable, '.')) {
                $parsedTypoScriptVariables[$variable] = $key;
            }
        }
        $this->variables = $parsedTypoScriptVariables;
        return $this->variables;
    }

    /**
     * @return array
     */
    protected function getTypoScriptSetup(): array
    {
        try {
            return $this->configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @return array
     */
    protected function getIncludedCSSFiles(): array
    {
        $cssFiles = $this->getTypoScriptSetup()['page.']['includeCSS.'] ?? [];
        $outputFiles = [];
        $lastKey = "";
        $lastValue = "";
        foreach ($cssFiles as $key => $value) {
            if (!str_ends_with($key, '.')) {
                if (!empty($lastKey)) {
                    $outputFiles[$lastValue]['file'] = $lastValue;
                }
                $lastKey = $key;
                $lastValue = $value;
                continue;
            }
            if (!empty($lastKey) && $lastKey . "." === $key) {
                $outputFiles[$lastValue] = [
                    'file' => $lastValue,
                    'tagAttributes' => $value
                ];
                $lastKey = "";
            }
        }
        if (!empty($lastKey)) {
            $outputFiles[$lastKey]['file'] = $lastKey;
        }
        return $outputFiles;
    }
}
