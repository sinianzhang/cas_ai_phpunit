<?php

namespace Hausformat\Scss;

use ScssPhp\ScssPhp\Exception\SassException;
use ScssPhp\ScssPhp\OutputStyle;
use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

class Compiler
{
    public const CACHE_KEY = 'hf_scss';

    /**
     * @param $scssContent
     * @param $variables
     * @param null $cssFilename
     * @param bool $useSourceMap
     * @param string $outputStyle
     *
     * @return string
     *
     * @throws FileDoesNotExistException
     * @throws NoSuchCacheException
     */
    public static function compileSassString($scssContent, $variables, $cssFilename = null, bool $useSourceMap = false, string $outputStyle = OutputStyle::COMPRESSED): string
    {

        $hash = sha1($scssContent);
        $tempScssFilePath = 'typo3temp/assets/scss/' . $hash.'.scss';
        $absoluteTempScssFilePath = GeneralUtility::getFileAbsFileName($tempScssFilePath);

        if (!file_exists($absoluteTempScssFilePath)) {
            GeneralUtility::mkdir_deep(dirname($absoluteTempScssFilePath));
            GeneralUtility::writeFile($absoluteTempScssFilePath,$scssContent);
        }

        return self::compileFile($tempScssFilePath, $variables, $cssFilename, $useSourceMap, $outputStyle);
    }

    /**
     * @param string      $scssFilePath
     * @param array       $variables
     * @param string|null $cssFilePath
     * @param bool        $useSourceMap
     * @param string      $outputStyle
     * @param string      $libImports
     *
     * @return string the compiled css file as path
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     * @throws \TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException
     */
    public static function compileFile(string $scssFilePath, array $variables, ?string $cssFilePath = null, bool $useSourceMap = false, string $outputStyle = OutputStyle::COMPRESSED, string $libImports = ''): string
    {
        $scssFilePath = GeneralUtility::getFileAbsFileName($scssFilePath);
        $sitePath = Environment::getPublicPath() . '/';

        if (!file_exists($scssFilePath)) {
            throw new FileDoesNotExistException($scssFilePath);
        }

        $calculatedContentHash = self::calculateContentHash($scssFilePath, $variables, $libImports);

        if ($cssFilePath === null) {
            // no target filename -> auto

            $pathInfo = pathinfo($scssFilePath);
            $filename = $pathInfo['filename'];
            $outputDir = 'typo3temp/assets/css/';
            $cssFilePath = $outputDir . $filename . $calculatedContentHash . '.css';
        }

        /** @var FileBackend $cache */
        $cache = GeneralUtility::makeInstance(CacheManager::class)->getCache(self::CACHE_KEY);

        $cacheKey = hash('sha1', $scssFilePath);
        $calculatedContentHash .= md5($cssFilePath);
        if ($useSourceMap) {
            $calculatedContentHash .= 'sm';
        }

        $calculatedContentHash .= $outputStyle;

        if ($cache->has($cacheKey)) {
            $contentHashCache = $cache->get($cacheKey);
            if ($contentHashCache === $calculatedContentHash) {
                return $cssFilePath;
            }
        }


        // Sass compiler cache
        $cacheDir = $sitePath . 'typo3temp/assets/scss/cache/';
        if (!is_dir($cacheDir)) {
            GeneralUtility::mkdir_deep($cacheDir);
        }
        if (!is_writable($cacheDir)) {
            // TODO: Error message
            return '';
        }

        $cacheOptions = [
            'cacheDir' => $cacheDir,
            'prefix' => md5($cssFilePath),
        ];


        $parser = new \ScssPhp\ScssPhp\Compiler($cacheOptions);
        $parser->addVariables($variables);
        $parser->setOutputStyle($outputStyle);
        if ($useSourceMap) {
            $parser->setSourceMap(\ScssPhp\ScssPhp\Compiler::SOURCE_MAP_INLINE);

            $parser->setSourceMapOptions([
                'sourceMapBasepath' => $sitePath,
                'sourceMapRootpath' => '/',
            ]);
        }

        try {
            $result = $parser->compileString($libImports.'@import "' . $scssFilePath . '";');
            $cache->set($cacheKey, $calculatedContentHash, ['scss'], 0);
            GeneralUtility::mkdir_deep(dirname(GeneralUtility::getFileAbsFileName($cssFilePath)));
            GeneralUtility::writeFile(GeneralUtility::getFileAbsFileName($cssFilePath), self::replaceUrls($result->getCss()));
        } catch (SassException  | \Exception $ex) {
            DebugUtility::debug($ex->getMessage());

            /** @var Logger $logger */
            $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
            $logger->error($ex->getMessage());
        }

        return $cssFilePath;
    }

    /**
     * Calculating content hash to detect changes
     *
     * @param string $scssFileName Existing scss file absolute path
     * @param array  $vars
     * @param string $libImports
     * @param array  $visitedFiles
     *
     * @return string
     */
    public static function calculateContentHash(string $scssFileName, array $vars = [], string $libImports = '', array $visitedFiles = []): string
    {
        if (\in_array($scssFileName, $visitedFiles, true)) {
            return '';
        }
        $visitedFiles[] = $scssFileName;

        $content = file_get_contents($scssFileName);
        $pathInfo = pathinfo($scssFileName);
        $hashContext = hash_init('sha1');
        hash_update($hashContext, $content);
        hash_update($hashContext, implode(',', $vars));
        hash_update($hashContext, $libImports);

        $imports = self::collectImports($content);
        foreach ($imports as $import) {
            $hashImport = '';

            if (file_exists($pathInfo['dirname'] . '/' . $import . '.scss')) {
                $hashImport = self::calculateContentHash($pathInfo['dirname'] . '/' . $import . '.scss', visitedFiles: $visitedFiles);
            } else {
                $parts = explode('/', $import);
                $filename = '_' . array_pop($parts);
                $parts[] = $filename;
                $path = $pathInfo['dirname'] . '/' . implode('/', $parts) . '.scss';
                if (file_exists($path)) {
                    $hashImport = self::calculateContentHash($path, visitedFiles: $visitedFiles);
                }
            }
            if ($hashImport !== '') {
                hash_update($hashContext, $hashImport);
            }
        }

        return hash_final($hashContext);
    }


    /**
     * Collect all @import files in the given content.
     *
     * @param string $content
     * @return array
     */
    private static function collectImports(string $content): array
    {
        $matches = [];
        $imports = [];

        preg_match_all('/@import([^;]*);/', $content, $matches);

        $scssImportListMatches = [];
        preg_match_all('/\/\/\s*@scss-import-list[^\n]*\n\$(\S+):\s*\(([^)]+)\)\s*/', $content, $scssImportListMatches);
        foreach ($matches[1] as $importString) {
            $files = explode(',', $importString);

            array_walk($files, function (string &$file) {
                $file = trim($file, " \t\n\r\0\x0B'\"");
            });

            $imports = array_merge($imports, $files);
        }

        foreach ($scssImportListMatches[2] as $importString) {
            $files = explode(',', str_replace(' ', '', $importString));
            array_walk($files, function (string &$file) {
                // remove ' " \n and trim
                $file = trim($file, " \t\n\r\0\x0B'\"");
            });
            $imports = array_merge($imports, $files);
        }
        $imports = array_filter($imports);
        // filter out values that start with #{$
        return array_filter($imports, function ($value) {
            return !str_starts_with($value, '#{$');
        });
    }

    /**
     * Replaces EXT: pahs in generated css with its public URL
     *
     * @param $string
     *
     * @return array|mixed|string|string[]
     * @throws \TYPO3\CMS\Core\Resource\Exception\InvalidFileException
     */
    private static function replaceUrls($string) {

        // Regular expression pattern to match URLs
        $pattern = '/\bEXT:[^\"\s]+/i';

        // Retrieve all URLs in the string
        preg_match_all($pattern, $string, $matches);
        // Loop through the matches and replace each URL
        foreach ($matches[0] as $url) {

            $absolutePath = GeneralUtility::getFileAbsFileName($url);
            $replacement = PathUtility::getAbsoluteWebPath($absolutePath);

            $string = str_replace($url, $replacement, $string);
        }

        return $string;
    }

}
