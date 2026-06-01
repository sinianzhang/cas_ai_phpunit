<?php

use B13\FaqT3demo\Controller\FaqController;

return [
    'web_faq' => [
        'parent' => 'content',
        'position' => ['before' => '*'],
        'access' => 'user',
        'iconIdentifier' => 'faq-module',
        'labels' => 'LLL:EXT:faq_t3demo/Resources/Private/Language/locallang_module.xlf',
        'inheritNavigationComponentFromMainModule' => false,
        'routes' => [
            '_default' => [
                'target' => FaqController::class . '::handleRequest',
            ],
        ],
    ],
];
