<?php

defined('TYPO3') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addRecordType(
    [
        'label' => 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:CType.contentstage.name',
        'description' => 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:CType.contentstage.description',
        'value' => 'contentstage',
        'icon' => 'content-image',
        'group' => 'default',
    ],
    '
        header,
        --palette--;;linklabelconfig,
        image,
    ',
);
