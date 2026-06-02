<?php

namespace Hausformat\ViewHelpers\ViewHelpers\Meta;

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
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Render a meta tag
 *
 * ## Examples
 *
 *     <hf:meta.tag name="description" content="This is a description" />
 *
 *     <hf:meta.tag name="keywords" content="keyword1, keyword2" />
 *
 *     <hf:meta.tag tagName="link" rel="canonical" href="https://www.example.com/canonical" />
 *
 * @group  hf-viewhelpers
 * @author .hausformat <entwicklung@hausformat.com>
 */
class TagViewHelper extends AbstractTagBasedViewHelper
{

    /**
     * @var string
     */
    protected $tagName = 'meta';

    /**
     * Arguments initialization
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('name', 'string', 'Name property the tag');
        $this->registerArgument('content', 'string', 'Content of meta tag');
        $this->registerArgument('http-equiv', 'string', 'Property: http-equiv');
        $this->registerArgument('scheme', 'string', 'Property: scheme');
        $this->registerArgument('lang', 'string', 'Property: lang');
        $this->registerArgument('dir', 'string', 'Property: dir');
        $this->registerArgument('tagName', 'string', 'Property: meta');
    }

    /**
     * Render method
     *
     * @return string
     */
    public function render(): string
    {
        if (($GLOBALS[ 'TYPO3_REQUEST' ] ?? null) instanceof ServerRequestInterface &&
            ApplicationType::fromRequest($GLOBALS[ 'TYPO3_REQUEST' ])->isBackend()) {
            return '';
        }
        if (isset($this->arguments[ 'content' ]) && (!empty($this->arguments[ 'content' ]) || $this->arguments[ 'content' ] === 0 || $this->arguments[ 'content' ] === '0')) {
            if ($this->arguments[ 'tagName' ]) {
                $this->tagName = $this->arguments[ 'tagName' ];
                $this->tag->setTagName($this->tagName);
            }

            if($this->arguments[ 'name' ]) {
                $this->tag->addAttribute('name', $this->arguments[ 'name' ]);
            }
            if($this->arguments[ 'http-equiv' ]) {
                $this->tag->addAttribute('http-equiv', $this->arguments[ 'http-equiv' ]);
            }
            if($this->arguments[ 'scheme' ]) {
                $this->tag->addAttribute('scheme', $this->arguments[ 'scheme' ]);
            }
            if($this->arguments[ 'lang' ]) {
                $this->tag->addAttribute('lang', $this->arguments[ 'lang' ]);
            }
            if($this->arguments[ 'dir' ]) {
                $this->tag->addAttribute('dir', $this->arguments[ 'dir' ]);
            }
            if($this->arguments[ 'content' ]) {
                $this->tag->addAttribute('content', $this->arguments[ 'content' ]);
            }


            /**
             * @var \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer
             */
            $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

            $pageRenderer->addHeaderData($this->tag->render());
        }
        return '';
    }
}
