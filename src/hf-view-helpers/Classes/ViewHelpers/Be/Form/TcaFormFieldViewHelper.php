<?php

namespace Hausformat\ViewHelpers\ViewHelpers\Be\Form;

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

use Hausformat\Lib\Service\BackendFieldRenderService;
use Hausformat\Lib\Service\GlobalsService;
use TYPO3\CMS\Backend\Form\FormResultCompiler;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Wraps a be form input field
 * Use this to get a consistant BE style that mimics the normal TYPO3 BE
 *
 * @author .hausformat <entwicklung@hausformat.com>
 */
class TcaFormFieldViewHelper extends AbstractViewHelper
{
    /**
     * @var BackendFieldRenderService
     */
    protected $backendFieldRendererService;

    /**
     * Specifies whether the escaping interceptors should be disabled or enabled for the render-result of this ViewHelper
     *
     * @see isOutputEscapingEnabled()
     *
     * @var boolean
     * @api
     */
    protected $escapeOutput = false;

    /**
     * @var \Hausformat\Lib\Service\GlobalsService
     *
     */
    protected $globalsService;


    /**
     * initialize Arguments
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('tableName', 'string', 'tablename witch contains the Field', true);
        $this->registerArgument('fieldName', 'string', 'The name of the field to Render', false, '');
        $this->registerArgument('row', 'array', 'the database entries of the row to display (prefill values)');
        $this->registerArgument('addJsAndCSS', 'bool', 'add nessesary JS and CSS to the Page', false, true);
        $this->registerArgument(
            'addLabel',
            'bool',
            'Adds the Label for the Field. (only needed if an fieldName is provided)',
            false,
            true
        );
        $this->registerArgument('storagePid', 'number', 'Set a PID on witch the Record should be Stored', false, 0);
    }

    public function injectBackendFieldRendererService(BackendFieldRenderService $backendFieldRendererService): void
    {
        $this->backendFieldRendererService = $backendFieldRendererService;
    }

    public function injectGlobalsService(GlobalsService $globalsService): void
    {
        $this->globalsService = $globalsService;
    }


    /**
     * Renders the content and wraps it with a class.
     *
     * @return string
     * @throws \TYPO3\CMS\Backend\Form\Exception
     */
    public function render(): string
    {
        $tableName = $this->arguments['tableName'];
        $fieldName = $this->arguments['fieldName'];
        $row = $this->arguments['row'];
        $storagePid = $this->arguments['storagePid'];

        $formResult = $this->backendFieldRendererService->render($tableName, $fieldName, $row, $storagePid);

        $labelHtml = '';
        if ($fieldName !== '' && isset($this->arguments['addLabel']) && $this->arguments['addLabel']) {
            $fieldTca = $this->globalsService->getTableTcaField($tableName, $fieldName);

            $label = $this->getLanguageService()->sL($fieldTca['label']);
            $labelHtml = '<label class="t3js-formengine-label">' .
                $label .
                '</label>';
        }

        if (isset($this->arguments['addJsAndCSS']) && $this->arguments['addJsAndCSS']) {
            $formResultCompiler = GeneralUtility::makeInstance(FormResultCompiler::class);
            $formResultCompiler->mergeResult($formResult);

            $formResultCompiler->addCssFiles();
            $formResultCompiler->printNeededJSFunctions();
        }

        return $labelHtml . $formResult['html'];
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
