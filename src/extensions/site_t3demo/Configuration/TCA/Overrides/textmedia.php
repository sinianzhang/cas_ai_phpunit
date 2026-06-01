<?php

defined('TYPO3') or die();

$GLOBALS['TCA']['tt_content']['types']['textmedia']['columnsOverrides']['assets']['description'] = 'LLL:EXT:site_t3demo/Resources/Private/Language/locallang_db.xlf:CType.textmedia.assets.description';
$GLOBALS['TCA']['tt_content']['types']['textmedia']['columnsOverrides']['assets']['config']['overrideChildTca']['types'] = $GLOBALS['TCA']['sys_file_reference']['defaultTypeConfiguration']['nolinkImageoverlay'];
