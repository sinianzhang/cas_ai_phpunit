<?php declare(strict_types=1);


namespace Hausformat\ViewHelpers\ViewHelpers\Asset;

use ScssPhp\ScssPhp\Exception\SassException;
use ScssPhp\ScssPhp\OutputStyle;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use Hausformat\Scss\Compiler;
use Hausformat\Scss\Service\ScssService;

/**
 * Add a SCSS or CSS file to the page. Processes SCSS files the same way as page.includeCSS (with variables etc.)
 * Also supports public paths.
 *
 * ## Examples
 *
 *     <hf:asset.stylesheet src="EXT:myExt/Resources/Public/Styles/myStyle.scss" />
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class StylesheetViewHelper extends AbstractTagBasedViewHelper
{

    protected $plainCss = '';

    protected AssetCollector $assetCollector;
    protected ScssService $scssService;

    public function injectAssetCollector(AssetCollector $assetCollector): void
    {
        $this->assetCollector = $assetCollector;
    }

    public function injectScssService(ScssService $scssService): void
    {
        $this->scssService = $scssService;
    }

    protected CacheManager $cacheManager;

    public function injectCacheManager(CacheManager $cacheManager): void
    {
        $this->cacheManager = $cacheManager;
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('as', 'string', 'Define the type of content being loaded (For rel="preload" or rel="prefetch" only).', false);
        $this->registerArgument('crossorigin', 'string', 'Define how to handle crossorigin requests.', false);
        $this->registerArgument('disabled', 'bool', 'Define whether or not the described stylesheet should be loaded and applied to the document.', false);
        $this->registerArgument('href', 'string', 'Define the URL of the resource (absolute or relative).', false);
        $this->registerArgument('hreflang', 'string', 'Define the language of the resource (Only to be used if \'href\' is set).', false);
        $this->registerArgument('importance', 'string', 'Define the relative fetch priority of the resource.', false);
        $this->registerArgument('integrity', 'string', 'Define base64-encoded cryptographic hash of the resource that allows browsers to verify what they fetch.', false);
        $this->registerArgument('media', 'string', 'Define which media type the resources applies to.', false);
        $this->registerArgument('referrerpolicy', 'string', 'Define which referrer is sent when fetching the resource.', false);
        $this->registerArgument('rel', 'string', 'Define the relationship of the target object to the link object.', false);
        $this->registerArgument('sizes', 'string', 'Define the icon size of the resource.', false);
        $this->registerArgument('type', 'string', 'Define the MIME type (usually \'text/css\').', false);
        $this->registerArgument('nonce', 'string', 'Define a cryptographic nonce (number used once) used to whitelist inline styles in a style-src Content-Security-Policy.', false);
        $this->registerArgument('useSourceMap', 'bool', 'Generate a SourceMap for the CSS-File.', false, false);
        $this->registerArgument('outputStyle', 'string', 'Compress the CSS-File. Allowed values: "compressed" and "expanded", default: compressed', false, OutputStyle::COMPRESSED);
        $this->registerArgument(
            'identifier',
            'string',
            'Use this identifier within templates to only inject your CSS once, even though it is added multiple times.',
            true
        );
        $this->registerArgument(
            'priority',
            'boolean',
            'Define whether the CSS should be included before other CSS. CSS will always be output in the <head> tag.',
            false,
            false
        );

        $this->registerArgument('vars', 'array', "Add SCSS variables like {title: data.header, other: 'hello world'}", false, []);
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Core\Cache\Exception
     * @throws \TYPO3\CMS\Core\Cache\Exception\InvalidDataException
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     * @throws \TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException
     */
    public function render(): string
    {
        $file = $this->arguments['href'];

        if (str_ends_with($file, '.scss')) {
            $this->renderSCSS($file);
        } else {
            $this->renderCSS($this->getPublicFilePath($file));
        }
        return '';
    }

    /**
     * Returns the path to the file
     * Handles private paths
     *
     * @param string $src
     *
     * @return string
     * @throws \TYPO3\CMS\Core\Cache\Exception
     * @throws \TYPO3\CMS\Core\Cache\Exception\InvalidDataException
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    protected function getPublicFilePath(string $src): string
    {
        if (!str_contains($src, '/Resources/Private/')) {
            return $src;
        }
        $src = GeneralUtility::getFileAbsFileName($src);
        $content = file_get_contents($src);

        $cacheFileName = hash('sha1', $src);
        $cacheContent = md5($content);
        $cacheFile = 'typo3temp/assets/css/' . md5($content) . '.css';

        $cache = $this->cacheManager->getCache('hflib');
        if ($cache->has($cacheFileName)) {
            if ($cache->get($cacheFileName) === $cacheContent && file_exists($cacheFile)) {
                return $cacheFile;
            }
            $cache->remove($cacheFileName);
        }
        $cacheFileCreated = GeneralUtility::writeStyleSheetContentToTemporaryFile($content);
        $cache->set($cacheFileName, $cacheContent, ['hf-lib-assets'], 0);
        if ($cacheFile !== $cacheFileCreated) {
            trigger_error('The assumed cache css file is inconsistent to the created cache css file!');
        }
        return $cacheFile;
    }

    /**
     * does exactly the same as f:asset.css
     *
     * @param string|null $file
     *
     * @return void
     */
    private function renderCSS(string|null $file): void
    {
        $identifier = (string)$this->arguments['identifier'];
        $attributes = $this->tag->getAttributes();

        // boolean attributes shall output attr="attr" if set
        if ($attributes['disabled'] ?? false) {
            $attributes['disabled'] = 'disabled';
        }
        unset($attributes['href']);
        $options = [
            'priority' => $this->arguments['priority'],
        ];
        if ($file !== null) {
            $this->assetCollector->addStyleSheet($identifier, $file, $attributes, $options);
        } else {
            $content = (string)$this->renderChildren();
            if ($content !== '') {
                $this->assetCollector->addInlineStyleSheet($identifier, $content, $attributes, $options);
            } elseif ($this->plainCss != '') {
                $this->assetCollector->addInlineStyleSheet($identifier, $this->plainCss, $attributes, $options);
            }
        }
    }

    /**
     * compile scss and overwrite filepath for normal css rendering
     *
     * @param string $file
     *
     * @throws \TYPO3\CMS\Core\Cache\Exception
     * @throws \TYPO3\CMS\Core\Cache\Exception\InvalidDataException
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     * @throws \TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException
     */
    private function renderSCSS(string $file): void
    {
        $variables = $this->arguments['vars'];


        $variables = array_merge($variables, $this->scssService->getVariables());
        $libImport = $this->scssService->getLibImport();
        try {
            $cssFile = Compiler::compileFile($file, $variables, libImports: $libImport);
        } catch (SassException $e) {
            return;
        }
        if ($cssFile !== '') {
            $this->renderCSS($cssFile);
        }
    }
}
