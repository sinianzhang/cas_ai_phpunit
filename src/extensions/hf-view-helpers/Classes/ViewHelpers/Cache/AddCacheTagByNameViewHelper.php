<?php

namespace Hausformat\ViewHelpers\ViewHelpers\Cache;

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

use Hausformat\ViewHelpers\ViewHelpers\Traits\WithCacheTagService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Adds a cache tag by name
 *
 * @group hf-viewhelpers
 * @author .hausformat <entwicklung@hausformat.com>
 */
class AddCacheTagByNameViewHelper extends AbstractViewHelper
{
    use WithCacheTagService;

    /**
     * @inheritdoc
     */
    public function render(): void
    {
        $tag = $this->arguments['tag'];
        $renderingContext = $this->renderingContext;
        self::getCacheTagService($renderingContext)->addCacheTag($tag);
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('tag', 'string', '', true);
    }
}
