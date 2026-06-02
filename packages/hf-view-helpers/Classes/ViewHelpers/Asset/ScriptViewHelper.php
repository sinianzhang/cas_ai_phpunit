<?php

declare(strict_types=1);

namespace Hausformat\ViewHelpers\ViewHelpers\Asset;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

/**
 * Add JavaScript to the page. Supports public and private paths.
 *
 * ## Examples
 *
 *     <hf:asset.script src="EXT:my_ext/Resources/Private/JavaScript/foo.js" />
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class ScriptViewHelper extends AbstractTagBasedViewHelper
{

    protected $tagName = 'script';

    /**
     * This VH does not produce direct output, thus does not need to be wrapped in an escaping node
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Rendered children string is passed as JavaScript code,
     * there is no point in HTML encoding anything from that.
     *
     * @var bool
     */
    protected $escapeChildren = false;

    protected AssetCollector $assetCollector;

    public function injectAssetCollector(AssetCollector $assetCollector): void
    {
        $this->assetCollector = $assetCollector;
    }

    protected CacheManager $cacheManager;

    public function injectCacheManager(CacheManager $cacheManager): void
    {
        $this->cacheManager = $cacheManager;
    }

    public function initialize(): void
    {
        // Add a tag builder, that does not html encode values, because rendering with encoding happens in AssetRenderer
        $this->setTagBuilder(
            new class () extends TagBuilder {
                public function addAttribute($attributeName, $attributeValue, $escapeSpecialCharacters = false): void
                {
                    parent::addAttribute($attributeName, $attributeValue, false);
                }
            }
        );
        parent::initialize();
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('async', 'bool', 'Define that the script will be fetched in parallel to parsing and evaluation.');
        $this->registerArgument('crossorigin', 'string', 'Define how to handle crossorigin requests.');
        $this->registerArgument('defer', 'bool', 'Define that the script is meant to be executed after the document has been parsed.');
        $this->registerArgument('integrity', 'string', 'Define base64-encoded cryptographic hash of the resource that allows browsers to verify what they fetch.');
        $this->registerArgument('nomodule', 'bool', 'Define that the script should not be executed in browsers that support ES2015 modules.');
        $this->registerArgument('nonce', 'string', 'Define a cryptographic nonce (number used once) used to whitelist inline styles in a style-src Content-Security-Policy.');
        $this->registerArgument('referrerpolicy', 'string', 'Define which referrer is sent when fetching the resource.');
        $this->registerArgument('src', 'string', 'Define the URI of the external resource.');
        $this->registerArgument('type', 'string', 'Define the MIME type (usually \'text/javascript\').');
        $this->registerArgument('useNonce', 'bool', 'Whether to use the global nonce value', false, false);
        $this->registerArgument(
            'identifier',
            'string',
            'Use this identifier within templates to only inject your JS once, even though it is added multiple times.',
            true
        );
        $this->registerArgument(
            'priority',
            'boolean',
            'Define whether the JavaScript should be put in the <head> tag above-the-fold or somewhere in the body part.',
            false,
            false
        );
        $this->registerArgument('inline', 'bool', 'Define whether or not the referenced file should be loaded as inline script (Only to be used if \'src\' is set).', false, false);
    }

    public function render(): string
    {
        $identifier = (string)$this->arguments['identifier'];
        $attributes = $this->tag->getAttributes();

        // boolean attributes shall output attr="attr" if set
        foreach (['async', 'defer', 'nomodule'] as $attribute) {
            if ($this->arguments[$attribute] ?? false) {
                $attributes[$attribute] = $attribute;
            }
        }

        $src = $attributes['src'] ?? null;

        if($src == null && isset($this->arguments['src']) && strlen($this->arguments['src']) > 0) {
            $src = (string) $this->arguments['src'];
        }
        unset($attributes['src']);
        $options = [
            'priority' => $this->arguments['priority'],
            'useNonce' => $this->arguments['useNonce'],
        ];
        if ($src !== null) {
            $src = $this->getPublicFilePath($src);
            if ($this->arguments['inline'] ?? false) {
                $content = @file_get_contents(GeneralUtility::getFileAbsFileName(trim($src)));
                if ($content !== false) {
                    $this->assetCollector->addInlineJavaScript($identifier, $content, $attributes, $options);
                }
            } else {
                $this->assetCollector->addJavaScript($identifier, $src, $attributes, $options);
            }
        } else {
            $content = (string)$this->renderChildren();
            if ($content !== '') {
                $this->assetCollector->addInlineJavaScript($identifier, $content, $attributes, $options);
            }
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
        $cacheFile = 'typo3temp/assets/js/' . md5($content) . '.js';

        $cache = $this->cacheManager->getCache('hflib');
        if ($cache->has($cacheFileName)) {
            if ($cache->get($cacheFileName) === $cacheContent && file_exists($cacheFile)) {
                return $cacheFile;
            }
            $cache->remove($cacheFileName);
        }
        $cacheFileCreated = GeneralUtility::writeJavaScriptContentToTemporaryFile($content);
        $cache->set($cacheFileName, $cacheContent, ['hf-lib-assets'], 0);
        if ($cacheFile !== $cacheFileCreated) {
            trigger_error('The assumed cache js file is inconsistent to the created cache js file!');
        }
        return $cacheFile;
    }
}
