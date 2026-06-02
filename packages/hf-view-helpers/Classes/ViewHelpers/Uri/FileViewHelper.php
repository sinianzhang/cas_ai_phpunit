<?php

namespace Hausformat\ViewHelpers\ViewHelpers\Uri;

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
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * Resizes a given image (if required) and returns its relative path.
 *
 * = Examples =
 *
 * <code title="Default">
 * <f:uri.image src="EXT:myext/Resources/Public/typo3_logo.png" />
 * </code>
 * <output>
 * typo3conf/ext/myext/Resources/Public/typo3_logo.png
 * or (in BE mode):
 * ../typo3conf/ext/myext/Resources/Public/typo3_logo.png
 * </output>
 *
 * <code title="Image Object">
 * <f:uri.image image="{imageObject}" />
 * </code>
 * <output>
 * fileadmin/images/image.png
 * or (in BE mode):
 * fileadmin/images/image.png
 * </output>
 *
 * <code title="Inline notation">
 * {f:uri.image(src: 'EXT:myext/Resources/Public/typo3_logo.png' minWidth: 30, maxWidth: 40)}
 * </code>
 * <output>
 * typo3temp/pics/[b4c0e7ed5c].png
 * (depending on your TYPO3s encryption key)
 * </output>
 *
 * <code title="non existing image">
 * <f:uri.image src="NonExistingImage.png" />
 * </code>
 * <output>
 * Could not get image resource for "NonExistingImage.png".
 * </output>
 *
 *
 * @author     .hausformat Development Team <support@hausformat.com>
 */
class FileViewHelper extends AbstractViewHelper
{
    /**
     * @var \TYPO3\CMS\Extbase\Service\ImageService
     */
    protected $imageService;

    /**
     * @param ImageService $imageService
     */
    public function injectImageService(ImageService $imageService) {
        $this->imageService = $imageService;
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('src', 'string', '', false);
        $this->registerArgument('file', 'mixed', '', false);
        $this->registerArgument('treatIdAsReference', 'boolean', '', false, false);
        $this->registerArgument('external', 'boolean', '', false, false);
    }

    /**
     * Resizes the image (if required) and returns its path. If the image was not resized, the path will be equal to $src
     *
     * @return string path to the image
     * @throws Exception
     */
    public function render()
    {
        $src = $this->arguments['src'];
        $file = $this->arguments['file'];
        $treatIdAsReference = $this->arguments['treatIdAsReference'];
        $external = $this->arguments['external'];

        if (is_null($src) && is_null($file) || !is_null($src) && !is_null($file)) {
            throw new Exception(
                'You must either specify a string src or a File object.',
                1382284105
            );
        }

        try {
            $file = $this->imageService->getImage($src, $file, $treatIdAsReference);
        } catch(\Exception $e)  {
            return '';
        }


        if (method_exists($file, 'getOriginalFile')) {
            // Get the original file from the file reference
            $file = $file->getOriginalFile();
        }

        $uri = $this->imageService->getImageUri($file);

        $baseUrl = $this->getBaseURL();

        if ($external && $baseUrl !== '' && !str_contains($uri, $baseUrl)) {
            $uri = static::combineUrls($uri, $baseUrl);
        }

        return $uri;
    }

    /**
     * @return mixed
     */
    protected function getBaseURL(): mixed
    {
        $request = $this->renderingContext->getAttribute(ServerRequestInterface::class);
        $site = $request->getAttribute('site');
        return (string)$site->getBase();
    }

    /**
     * @param string $url
     * @param string $baseUrl
     *
     * @return string
     */
    protected static function combineUrls($url, $baseUrl)
    {
        if ($url[0] === '/' && $baseUrl[strlen($baseUrl) - 1] === '/') {
            $url = substr($url, 0, 1);
        }
        if ($url[0] !== '/' && $baseUrl[strlen($baseUrl) - 1] !== '/') {
            $url = '/' . $url;
        }

        return $baseUrl . $url;
    }
}
