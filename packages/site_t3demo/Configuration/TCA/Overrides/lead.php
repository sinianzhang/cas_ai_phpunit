<?php

defined('TYPO3') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addRecordType(
    [
        'label' => 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:CType.lead.name',
        'description' => 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:CType.lead.description',
        'value' => 'lead',
        'icon' => 'content-header',
        'group' => 'default',
    ],
    '
        bodytext,
    ',
);
