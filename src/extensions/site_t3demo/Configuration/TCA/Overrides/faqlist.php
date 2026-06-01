<?php

defined('TYPO3') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addRecordType(
    [
        'label' => 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:CType.faqlist.name',
        'description' => 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:CType.faqlist.description',
        'value' => 'faqlist',
        'icon' => 'mimetypes-x-faq_t3demo',
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
                ],
            ],
        ],
    ],
);
