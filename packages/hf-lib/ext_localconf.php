<?php

defined('TYPO3') or die();


$GLOBALS['TYPO3_CONF_VARS']['SYS']['routing']['aspects']['PassthroughValueMapper'] = \Hausformat\Lib\Routing\Aspect\PassthroughValueMapper::class;

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\Hausformat\Lib\Service\Cache\CacheTagServiceInterface::class] = [
    'className' => \Hausformat\Lib\Service\Cache\PagesCacheTagService::class
];

if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['hflib'] ?? null)) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['hflib'] = [
        'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
        'backend' => \TYPO3\CMS\Core\Cache\Backend\FileBackend::class,
        'options' => [
            'defaultLifetime' => 0,
        ]
    ];
}
