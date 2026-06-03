<?php

defined('TYPO3') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addRecordType(
    [
        'label' => 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:CType.footerinfo.name',
        'description' => 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:CType.footerinfo.description',
        'value' => 'footerinfo',
        'icon' => 'content-info',
        'group' => 'special',
    ],
    '
        header,
        bodytext,
        --palette--;;linklabel,
    ',
);
