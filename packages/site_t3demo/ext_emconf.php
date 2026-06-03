<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Site Package for demo.typo3.org',
    'description' => 'Site Package for demo.typo3.org',
    'category' => 'fe',
    'author' => 'David Steeb',
    'author_email' => 'typo3@b13.com',
    'state' => 'stable',
    'uploadfolder' => 0,
    'clearCacheOnLoad' => 1,
    'author_company' => 'b13 GmbH, Stuttgart',
    'version' => '1.0.0',
    'constraints' =>
        [
            'depends' =>
                [
                    'bolt' => '*',
                    'cta' => '*',
                ],
        ],
];
