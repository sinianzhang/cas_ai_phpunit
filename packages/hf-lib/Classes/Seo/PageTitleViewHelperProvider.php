<?php

namespace Hausformat\Lib\Seo;

use TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider;

/**
 * PageTitleViewHelperProvider
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class PageTitleViewHelperProvider extends AbstractPageTitleProvider
{
    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }
}
