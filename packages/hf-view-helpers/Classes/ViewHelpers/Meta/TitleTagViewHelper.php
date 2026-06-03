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

use Hausformat\Lib\Seo\PageTitleViewHelperProvider;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Set the title tag
 *
 * ## Examples
 *
 *     <hf:meta.titleTag>myTitle</hf:titleTag>
 *     <title>default title - myTitle</title>
 *
 *     <hf:meta.titleTag mode="prepend" divider=" / ">myTitle</hf:titleTag>
 *     <title>myTitle / default title</title>
 *
 * @group  hf-viewhelpers
 * @author .hausformat <entwicklung@hausformat.com>
 */
class TitleTagViewHelper extends AbstractViewHelper
{
    /** @var string */
    protected const MODE_APPEND = 'append';

    /** @var string */
    protected const MODE_PREPEND = 'prepend';

    /** @var string */
    protected const MODE_OVERRIDE = 'override';

    /** @var string */
    protected const MODE_OVERWRITE = 'overwrite';

    /** @var string */
    protected const DEFAULT_DIVIDER = ' - ';

    /**
     * @return void
     */
    public function render(): void
    {
        $content = $this->arguments[ 'value' ] ?? $this->renderChildren();
        if (empty($content)) {
            return;
        }

        $request = $this->renderingContext->getAttribute(ServerRequestInterface::class);
        $pageInformation = $request->getAttribute('frontend.page.information');
        $oldTitle = $pageInformation->getPageRecord()['title'] ?? '';

        $title = self::getTitle(
            $oldTitle,
            $content,
            $this->arguments[ 'divider' ],
            $this->arguments[ 'mode' ]
        );

        GeneralUtility::makeInstance(PageTitleViewHelperProvider::class)->setTitle($title);
    }

    /**
     * @param string $pageTitle
     * @param string $title
     * @param string $divider
     * @param string $mode
     *
     * @return string
     */
    protected static function getTitle(string $pageTitle, string $title, string $divider, string $mode): string
    {
        switch ($mode) {
            case self::MODE_APPEND:
                return $pageTitle . $divider . $title;
            case self::MODE_PREPEND:
                return $title . $divider . $pageTitle;
            case self::MODE_OVERRIDE:
            case self::MODE_OVERWRITE:
                return $title;
            default:
                throw new \InvalidArgumentException(
                    sprintf(
                        'Invalid mode "%s". Valid modes are: %s, %s and %s.',
                        $mode,
                        self::MODE_APPEND,
                        self::MODE_PREPEND,
                        self::MODE_OVERWRITE
                    )
                );
        }
    }

    public function initializeArguments(): void
    {
        $this->registerArgument(
            'mode',
            'string',
            'How the content is merged with the current page title (append, prepend, override, overwrite)',
            false,
            self::MODE_APPEND
        );

        $this->registerArgument(
            'divider',
            'string',
            'The string used to divide the current page title from the content',
            false,
            self::DEFAULT_DIVIDER
        );

        $this->registerArgument('value', 'string', 'The title to set');
    }
}
