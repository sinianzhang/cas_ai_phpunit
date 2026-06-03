<?php

// clear the default items for "layout" field to allow for consistent adding of additional items with addItems in
// PageTSConfig (instead of a combination of altLabels and addItems
$GLOBALS['TCA']['tt_content']['columns']['layout']['config']['items'] = [];
unset($GLOBALS['TCA']['tt_content']['columns']['layout']['config']['default']);

$GLOBALS['TCA']['tt_content']['columns']['imageorient']['config']['items'] = [
    [
        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:imageorient.I.0',
        'value' => 0,
        'icon' => 'content-beside-text-img-above-center',
    ], [
        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:imageorient.I.6',
        'value' => 17,
        'icon' => 'content-inside-text-img-right',
    ], [
        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:imageorient.I.7',
        'value' => 18,
        'icon' => 'content-inside-text-img-left',
    ],
];

$GLOBALS['TCA']['tt_content']['columns']['tx_cta_linkconfig']['config']['items'] = [
    [
        'label' => 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:cta.linkconfig.I.0',
        'value' => 0,
    ], [
        'label' => 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:cta.linkconfig.I.primary',
        'value' => 'primary',
    ], [
        'label' => 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:cta.linkconfig.I.secondary',
        'value' => 'secondary',
    ],
];

$GLOBALS['TCA']['tt_content']['columns']['header_layout']['config']['items'] = [
    [
        'label' => 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:tt_content.header_layout.I.default_value',
        'value' => '0',
    ], [
        'label' => 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:tt_content.header_layout.I.1',
        'value' => '1',
    ], [
        'label' => 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:tt_content.header_layout.I.2',
        'value' => '2',
    ], [
        'label' => 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:tt_content.header_layout.I.3',
        'value' => '3',
    ], [
        'label' => 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:tt_content.header_layout.I.4',
        'value' => '4',
    ], [
        'label' => 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:tt_content.header_layout.I.5',
        'value' => '5',
    ], [
        'label' => 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:tt_content.header_layout.I.100',
        'value' => '100',
    ],
];

$GLOBALS['TCA']['tt_content']['types']['menu_pages']['columnsOverrides']['pages']['description'] =
    'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:tt_content.pages.types.menu_pages.description'
;

$GLOBALS['TCA']['tt_content']['types']['menu_subpages']['columnsOverrides']['pages']['description'] =
    'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:tt_content.pages.types.menu_subpages.description'
;

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--palette--;;linklabelconfig',
    'menu_pages',
    'after:pages'
);
