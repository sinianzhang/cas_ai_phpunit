<?php

$GLOBALS['TYPO3_CONF_VARS']['BE']['debug'] = true;
$GLOBALS['TYPO3_CONF_VARS']['FE']['debug'] = true;

$GLOBALS['TYPO3_CONF_VARS']['LOG']['TYPO3']['CMS']['deprecations']['writerConfiguration']['notice']['TYPO3\CMS\Core\Log\Writer\FileWriter']['disabled'] = false;
$GLOBALS['TYPO3_CONF_VARS']['SYS']['devIPmask'] = '*';
$GLOBALS['TYPO3_CONF_VARS']['SYS']['belogErrorReporting'] = 32767;

$GLOBALS['TYPO3_CONF_VARS']['LOG']['writerConfiguration'] = [
    'warning' => [
        'TYPO3\CMS\Core\Log\Writer\FileWriter' => ['disabled' => false]
    ]
];

$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['content_sync']['sourceNode'] = [
    'basePath' => '/home/demo-content/content.demo.typo3.org/shared',
    'bin' => '/home/demo-content/content.demo.typo3.org/current/bin/typo3',
    'connection' => 'demo-content@demo02.typo3server.ch',
    'local' => '0',
];
$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['content_sync']['targetNode'] = [
    'basePath' => '/home/demo-test/test.demo.typo3.org/shared',
    'bin' => '/home/demo-test/test.demo.typo3.org/current/bin/typo3',
    'connection' => '',
    'local' => '1',
];
