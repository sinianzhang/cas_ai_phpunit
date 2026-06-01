<?php

defined('TYPO3') or die();

(function () {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['tx-faq_t3demo'] = \B13\FaqT3demo\Hooks\DataHandlerCacheHook::class;
})();
