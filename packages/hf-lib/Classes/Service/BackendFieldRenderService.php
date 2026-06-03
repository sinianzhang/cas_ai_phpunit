<?php

namespace Hausformat\Lib\Service;

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
use TYPO3\CMS\Backend\Form\FormDataCompiler;
use TYPO3\CMS\Backend\Form\FormDataGroup\TcaDatabaseRecord;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Service class for rendering backend fields based on TYPO3's TCA configuration.
 * Utilizes the NodeFactory and FormDataCompiler to generate backend forms dynamically for given database records.
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class BackendFieldRenderService
{

    /**
     * @var \TYPO3\CMS\Backend\Form\NodeFactory
     */
    protected $nodeFactory;

    /**
     * @param string $tableName
     * @param string $fieldName
     * @param array $row
     * @param int $pid
     *
     * @return array
     * @throws \TYPO3\CMS\Backend\Form\Exception
     */
    public function render($tableName, $fieldName = '', $row = null, $pid = 0)
    {

        $formDataGroup = GeneralUtility::makeInstance(TcaDatabaseRecord::class);
        $formDataCompiler = GeneralUtility::makeInstance(FormDataCompiler::class, $formDataGroup);
        $nodeFactory = GeneralUtility::makeInstance(NodeFactory::class);
        $formDataCompilerInput = [
            'tableName' => $tableName,
            'vanillaUid' => (int)$pid,
            'command' => 'new',
        ];

        if(isset($row['uid'])) {
            $formDataCompilerInput['command'] = 'edit';
        }

        if($row instanceof ObjectStorage) {
            $newArray = [];
            /** @var AbstractEntity $subObject */
            foreach ($row as $subObject) {
                $newArray[] = $subObject->getUid();
            }
            $uids = '';
            if(count($newArray) > 0) {
                $uids = implode(',', $newArray);
            }


            $row = [$fieldName => $uids];
        }

        if ($row !== null) {
            $formDataCompilerInput['databaseRow'] = $row;
        }

        $formDataCompilerInput['request'] = $GLOBALS['TYPO3_REQUEST'] ?? GeneralUtility::makeInstance(ServerRequestInterface::class);

        $formData = $formDataCompiler->compile($formDataCompilerInput,  GeneralUtility::makeInstance(TcaDatabaseRecord::class));
        if ($fieldName !== '') {
            $formData['renderType'] = 'singleFieldContainer';
            $formData['fieldName'] = $fieldName;
        } else {
            $formData['renderType'] = 'outerWrapContainer';
        }

        return $nodeFactory->create($formData)->render();
    }
}
