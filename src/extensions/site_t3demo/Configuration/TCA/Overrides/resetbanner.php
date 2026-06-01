<?php

defined('TYPO3') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addRecordType(
    [
        'label' => 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:CType.resetbanner.name',
        'description' => 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:CType.resetbanner.description',
        'value' => 'resetbanner',
        'icon' => 'content-clock',
        'group' => 'special',
    ],
    '
        header,
        bodytext,
    ',
    [
        'columnsOverrides' => [
            'bodytext' => [
                'config' => [
                    'rows' => 5,
                ],
            ],
        ],
    ]
);
