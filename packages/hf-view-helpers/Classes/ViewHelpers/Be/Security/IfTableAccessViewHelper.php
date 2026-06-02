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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * Check if the current user has access to a table. ViewHelper acts like a condition ViewHelper. (f:then and f:else)
 *
 * ## Examples
 *
 *     <hf:be.security.ifTableAccess table="tt_content">
 *         <f:then>
 *             user has access to tt_content
 *         </f:then>
 *     </hf:be.security.ifTableAccess>
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class IfTableAccessViewHelper extends AbstractConditionViewHelper
{

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('table', 'mixed', 'The Table or Object you want to access', true);
        $this->registerArgument('action', 'string', 'The action you want to take', false, 'list');
    }


    /**
     * Renders <f:then> child if any BE user is currently authenticated, otherwise renders <f:else> child.
     *
     * @return string the rendered string
     * @api
     */
    public function render(): string
    {
        // get table eighter from class name or the passed argument
        if(is_object($this->arguments['table'])) {
            $table = get_class($this->arguments['table']);
        } else {
            $table = $this->arguments['table'];
        }
        if (str_contains($table, '\\')) {
            $dataMapper = GeneralUtility::makeInstance(DataMapper::class);
            $table = $dataMapper->convertClassNameToTableName($table);
        } else {
            $table = strtolower($table);
        }

        // get the action
        switch (strtolower($this->arguments['action'])) {
            case 'edit':
            case 'modify':
                $action = 'modify';
                break;
            case 'listing':
            case 'list':
            default:
                $action = 'select';
                break;
        }

        // get config from exclude Fields
        $grantAccess = GlobalsService::getInstance()->getBackendUser()->check('tables_' . $action, $table);

        // render then, if the exclude field is allowed for this user
        if (GlobalsService::getInstance()->hasGlobal('BE_USER') && $grantAccess) {
            return $this->renderThenChild();
        }

        return $this->renderElseChild();
    }
}
