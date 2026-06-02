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

use Hausformat\Lib\Context\Context;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Returns the uid of the current language.
 *
 * @group  hf-viewhelpers
 * @author .hausformat <entwicklung@hausformat.com>
 */
class SysLanguageUidViewHelper extends AbstractViewHelper
{
    /**
     * @var \Hausformat\Lib\Context\Context
     */
    protected $context;

    /**
     * @param Context $context
     *
     * @return void
     */
    public function injectContext(Context $context): void
    {
        $this->context = $context;
    }

    /**
     * @return int
     */
    public function render(): int
    {
        return $this->context->getLanguageAspect()->getId();
    }
}
