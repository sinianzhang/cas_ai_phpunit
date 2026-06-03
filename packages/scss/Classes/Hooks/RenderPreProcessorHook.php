<?php declare(strict_types=1);

namespace Hausformat\Scss\Hooks;

/***************************************************************
 *  Copyright notice
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use ScssPhp\ScssPhp\Exception\SassException;
use ScssPhp\ScssPhp\OutputStyle;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use Hausformat\Scss\Compiler;
use Hausformat\Scss\Service\ScssService;

/**
 * Hook to preprocess scss files
 */
class RenderPreProcessorHook
{

    private $variables = [];

    /**
     * @var \Hausformat\Scss\Service\ScssService
     */
    protected $scssService;

    /**
     * Main hook function
     *
     * @param array        $params       Array of CSS/javascript and other files
     * @param PageRenderer $pagerenderer Pagerenderer object
     *
     * @return void
     * @throws FileDoesNotExistException
     * @throws NoSuchCacheException
     */
    public function renderPreProcessorProc(array &$params, PageRenderer $pagerenderer): void
    {
        if ($GLOBALS['TYPO3_REQUEST'] == null ||
            !ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend()
        ) {
            return;
        }

        if (!\is_array($params['cssFiles'])) {
            return;
        }

        if ($this->scssService === null) {
            $this->scssService = GeneralUtility::makeInstance(ScssService::class);
        }

        $this->variables = $this->scssService->getVariables();
        $libImport = $this->scssService->getLibImport();

        // we need to rebuild the CSS array to keep order of CSS files
        $cssFiles = [];
        foreach ($params['cssFiles'] as $file => $conf) {
            $pathInfo = pathinfo($conf['file']);

            if (!isset($pathInfo['extension']) || $pathInfo['extension'] !== 'scss') {
                $cssFiles[$file] = $conf;
                continue;
            }

            $outputFilePath = null;
            $outputStyle = OutputStyle::COMPRESSED;

            // search settings for scss file
            $tagAttributes = $conf['tagAttributes'] ?? [];
            $outputFilePath = $tagAttributes['outputfile'] ?? null;
            if (isset($tagAttributes['outputStyle']) && ($tagAttributes['outputStyle'] === 'expanded' || $tagAttributes['outputStyle'] === 'compressed')) {
                $outputStyle = $tagAttributes['outputStyle'];
            }
            $useSourceMap = isset($tagAttributes['sourceMap']) && $tagAttributes['sourceMap'];
            $inlineOutput = isset($tagAttributes['inlineOutput']) && $tagAttributes['inlineOutput'];

            $scssFilePath = GeneralUtility::getFileAbsFileName($conf['file']);
            $pathChunks = explode('/', PathUtility::getAbsoluteWebPath($scssFilePath));
            $assetPath = implode('/', array_splice($pathChunks, 0, 3)) . 'RenderPreProcessorHook.php/';

            if ($inlineOutput) {
                $useSourceMap = false;
            }

            try {
                $cssFilePath = Compiler::compileFile(
                    $scssFilePath,
                    array_merge($this->variables, ['extAssetPath' => $assetPath]),
                    $outputFilePath,
                    $useSourceMap,
                    $outputStyle,
                    $libImport);
            } catch (SassException|NoSuchCacheException|FileDoesNotExistException $e) {
                continue;
            }

            if ($inlineOutput) {
                unset($cssFiles[$file]);

                // TODO: compression
                $params['cssInline'][$file] = [
                    'code' => file_get_contents(GeneralUtility::getFileAbsFileName($cssFilePath)),
                    'forceOnTop' => false,
                ];
            } else {
                $cssFiles[$cssFilePath] = $params['cssFiles'][$file];
                $cssFiles[$cssFilePath]['file'] = $cssFilePath;
            }
        }

        if ($libImport !== '') {
            foreach ($cssFiles as $key => $cssFile) {
                if (isset($cssFile['tagAttributes']['isLib']) && $cssFile['tagAttributes']['isLib'] === "1") {
                    unset($cssFiles[$key]);
                }
            }
        }

        $params['cssFiles'] = $cssFiles;
    }
}
