<?php

defined('TYPO3') or die();

(function () {
    $GLOBALS['TYPO3_CONF_VARS']['GFX']['gdlib_png'] = 1;
    $GLOBALS['TYPO3_CONF_VARS']['GFX']['gif_compress'] = 0;
    $GLOBALS['TYPO3_CONF_VARS']['GFX']['jpg_quality'] = 90;

    $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['default'] = 'EXT:site_t3demo/Configuration/RTE/RteConfiguration.yaml';
    $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['rteWithTable'] = 'EXT:site_t3demo/Configuration/RTE/RteWithTable.yaml';

    // remove the default textmedia preview renderer (we want to use our own preview templates
    unset(
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']['textmedia'],
    );
})();
