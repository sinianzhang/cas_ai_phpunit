<?php

defined('TYPO3') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addRecordType(
    [
        'label' => 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:CType.ingredients.name',
        'description' => 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:CType.ingredients.description',
        'value' => 'ingredients',
        'icon' => 'content-table',
        'group' => 'special',
    ],
    '
        --palette--;;headers,
        bodytext,
    ',
    [
        'columnsOverrides' => [
            'bodytext' => [
                'config' => [
                    'enableRichtext' => true,
                    'richtextConfiguration' => 'rteWithTable',
                ],
            ],
        ],
    ]
);
