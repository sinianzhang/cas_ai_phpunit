<?php

$GLOBALS['TCA']['sys_file_reference']['palettes']['basicImageoverlayPalette']['showitem'] = 'title,alternative,--linebreak--,crop';
$GLOBALS['TCA']['sys_file_reference']['palettes']['basicVideooverlayPalette']['showitem'] = 'title,alternative,--linebreak--,autoplay';
$GLOBALS['TCA']['sys_file_reference']['palettes']['basicAudiooverlayPalette']['showitem'] = 'title,--linebreak--,autoplay';
$GLOBALS['TCA']['sys_file_reference']['palettes']['nolinkImageoverlayPalette']['showitem'] = 'title,alternative,--linebreak--,description,crop';

// Basic Overlay: Basically the same as the default TYPO3 setting
$GLOBALS['TCA']['sys_file_reference']['defaultTypeConfiguration']['basicoverlay'] = $GLOBALS['TCA']['sys_file_reference']['types'];
$GLOBALS['TCA']['sys_file_reference']['defaultTypeConfiguration']['basicoverlay'][\TYPO3\CMS\Core\Resource\FileType::AUDIO->value]['showitem'] =
    '--palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.audioOverlayPalette;audioOverlayPalette,
     --palette--;;filePalette';
$GLOBALS['TCA']['sys_file_reference']['defaultTypeConfiguration']['basicoverlay'][\TYPO3\CMS\Core\Resource\FileType::VIDEO->value]['showitem'] =
    '--palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.videoOverlayPalette;videoOverlayPalette,
     --palette--;;filePalette';

// Basic Image Overlay: Shows title, alternative, and crop settings for images, no description for all media types
$GLOBALS['TCA']['sys_file_reference']['defaultTypeConfiguration']['basicImageoverlay'] = $GLOBALS['TCA']['sys_file_reference']['types'];
$GLOBALS['TCA']['sys_file_reference']['defaultTypeConfiguration']['basicImageoverlay'][\TYPO3\CMS\Core\Resource\FileType::IMAGE->value]['showitem'] =
    '--palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;basicImageoverlayPalette,
     --palette--;;filePalette';
$GLOBALS['TCA']['sys_file_reference']['defaultTypeConfiguration']['basicImageoverlay'][\TYPO3\CMS\Core\Resource\FileType::AUDIO->value]['showitem'] =
    '--palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.audioOverlayPalette;basicAudiooverlayPalette,
     --palette--;;filePalette';
$GLOBALS['TCA']['sys_file_reference']['defaultTypeConfiguration']['basicImageoverlay'][\TYPO3\CMS\Core\Resource\FileType::VIDEO->value]['showitem'] =
    '--palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.videoOverlayPalette;basicVideooverlayPalette,
     --palette--;;filePalette';

// No Link Overlay: Shows full overlay palettes for all file types, without any link field, to not confuse editors if the link is not used in your frontend templates
$GLOBALS['TCA']['sys_file_reference']['defaultTypeConfiguration']['nolinkImageoverlay'] = $GLOBALS['TCA']['sys_file_reference']['types'];
$GLOBALS['TCA']['sys_file_reference']['defaultTypeConfiguration']['nolinkImageoverlay'][\TYPO3\CMS\Core\Resource\FileType::IMAGE->value]['showitem'] =
    '--palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;nolinkImageoverlayPalette,
     --palette--;;filePalette';
$GLOBALS['TCA']['sys_file_reference']['defaultTypeConfiguration']['nolinkImageoverlay'][\TYPO3\CMS\Core\Resource\FileType::AUDIO->value]['showitem'] =
    '--palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.audioOverlayPalette;audioOverlayPalette,
     --palette--;;filePalette';
$GLOBALS['TCA']['sys_file_reference']['defaultTypeConfiguration']['nolinkImageoverlay'][\TYPO3\CMS\Core\Resource\FileType::VIDEO->value]['showitem'] =
    '--palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.videoOverlayPalette;videoOverlayPalette,
     --palette--;;filePalette';

// Show only cropping options (useful for keyvisuals, background images, etc., where there is no frontend output of any additional title/alternative/description data
$GLOBALS['TCA']['sys_file_reference']['defaultTypeConfiguration']['cropImageoverlay'] = [
    0 => [
        'showitem' => '
            --palette--;;filePalette',
    ],
    \TYPO3\CMS\Core\Resource\FileType::TEXT->value => [
        'showitem' => '
            --palette--;;filePalette',
    ],
    \TYPO3\CMS\Core\Resource\FileType::IMAGE->value => [
        'showitem' => '
            crop,
            --palette--;;filePalette',
    ],
    \TYPO3\CMS\Core\Resource\FileType::AUDIO->value => [
        'showitem' => '
            --palette--;;filePalette',
    ],
    \TYPO3\CMS\Core\Resource\FileType::VIDEO->value => [
        'showitem' => '
            --palette--;;filePalette',
    ],
    \TYPO3\CMS\Core\Resource\FileType::APPLICATION->value => [
        'showitem' => '
            --palette--;;filePalette',
    ],
];
