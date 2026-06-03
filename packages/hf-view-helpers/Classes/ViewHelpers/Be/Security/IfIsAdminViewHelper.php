<?php

namespace Hausformat\ViewHelpers\ViewHelpers\Be\Security;

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

use Hausformat\Lib\Service\GlobalsService;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * Check if the current user is logged in as admin. ViewHelper acts like a condition ViewHelper. (f:then and f:else)
 *
 * ## Examples
 *
 *     <hf:be.security.ifIsAdmin>
 *         <f:then>
 *             is logged in as admin
 *         </f:then>
 *     </hf:be.security.ifIsAdmin>
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class IfIsAdminViewHelper extends AbstractConditionViewHelper
{
    /**
     * @return mixed
     */
    public function render(): mixed
    {
        if (static::verdict($this->arguments, $this->renderingContext)) {
            return $this->renderThenChild();
        }

        return $this->renderElseChild();
    }

    /**
     * @param array $arguments
     *
     * @param RenderingContextInterface|null $renderingContext
     *
     * @return bool
     */
    public static function verdict(array $arguments, ?RenderingContextInterface $renderingContext = null): bool
    {
        $backendUser = GlobalsService::getInstance()->getBackendUser();

        return $backendUser && $backendUser->user['admin'];
    }
}
