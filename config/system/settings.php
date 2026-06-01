<?php
return [
    'BE' => [
        'debug' => false,
        'installToolPassword' => 'joh316',
        'passwordHashing' => [
            'className' => 'TYPO3\\CMS\\Core\\Crypto\\PasswordHashing\\Argon2iPasswordHash',
            'options' => [],
        ],
    ],
    'DB' => [
        'Connections' => [
            'Default' => [
                'charset' => 'utf8mb4',
                'dbname' => false,
                'defaultTableOptions' => [
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                ],
                'driver' => 'mysqli',
                'host' => false,
                'password' => false,
                'port' => 3306,
                'user' => false,
            ],
        ],
    ],
    'EXTCONF' => [
        'lang' => [],
    ],
    'EXTENSIONS' => [
        'backend' => [
            'backendFavicon' => '',
            'backendLogo' => '',
            'loginBackgroundImage' => 'EXT:site_t3demo/Resources/Public/Backend/Login/login-background.svg',
            'loginFootnote' => 'TYPO3 made with ❤ by b13',
            'loginHighlightColor' => '#B11030',
            'loginLogo' => 'EXT:site_t3demo/Resources/Public/Assets/SVGs/logo.svg',
            'loginLogoAlt' => '',
        ],
        'content_sync' => [
            'configuration' => [
                'databaseTables' => '',
                'excludeDatabaseTables' => 'sys_registry,sys_log,cache_*,tx_extensionmanager_domain_model_*,tx_contentsync_job,tx_scheduler_*',
                'syncFiles' => 'web/fileadmin',
            ],
            'sourceNode' => [
                'basePath' => '',
                'bin' => '',
                'connection' => '',
                'local' => '1',
            ],
            'targetNode' => [
                'basePath' => '',
                'bin' => '',
                'connection' => '',
                'local' => '0',
            ],
        ],
        'extensionmanager' => [
            'automaticInstallation' => '1',
            'offlineMode' => '0',
        ],
        'faq_t3demo' => [
            'pid' => 36,
        ],
        'redirects' => [
            'showCheckIntegrityInfoInReports' => '1',
            'showCheckIntegrityInfoInReportsSeconds' => '86400',
        ],
        'scheduler' => [
            'maxLifetime' => '5',
            'showSampleTasks' => '0',
        ],
        'schema' => [
            'allowOnlyOneBreadcrumbList' => '0',
            'automaticBreadcrumbExcludeAdditionalDoktypes' => '',
            'automaticBreadcrumbSchemaGeneration' => '1',
            'automaticWebPageSchemaGeneration' => '0',
            'embedMarkupInBodySection' => '1',
            'embedMarkupOnNoindexPages' => '1',
        ],
    ],
    'FE' => [
        'debug' => false,
        'disableNoCacheParameter' => true,
        'passwordHashing' => [
            'className' => 'TYPO3\\CMS\\Core\\Crypto\\PasswordHashing\\Argon2iPasswordHash',
            'options' => [],
        ],
    ],
    'GFX' => [
        'processor' => 'GraphicsMagick',
        'processor_effects' => false,
        'processor_enabled' => true,
        'processor_path' => '/usr/bin/',
    ],
    'LANG' => [
        'availableLocales' => [
            'de',
        ],
    ],
    'LOG' => [
        'TYPO3' => [
            'CMS' => [
                'deprecations' => [
                    'writerConfiguration' => [
                        'notice' => [
                            'TYPO3\CMS\Core\Log\Writer\FileWriter' => [
                                'disabled' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'writerConfiguration' => [
            'error' => [
                'TYPO3\CMS\Core\Log\Writer\FileWriter' => [
                    'disabled' => false,
                ],
            ],
            'warning' => [
                'TYPO3\CMS\Core\Log\Writer\FileWriter' => [
                    'disabled' => true,
                ],
            ],
        ],
    ],
    'MAIL' => [
        'transport' => 'sendmail',
        'transport_sendmail_command' => '/usr/sbin/sendmail -t -i',
        'transport_smtp_encrypt' => '',
        'transport_smtp_password' => '',
        'transport_smtp_server' => '',
        'transport_smtp_username' => '',
    ],
    'SYS' => [
        'belogErrorReporting' => 32765,
        'devIPmask' => '127.0.0.1,::1',
        'displayErrors' => -1,
        'encryptionKey' => 'a-random-encryption-key',
        'exceptionalErrors' => 4341,
        'features' => [
            'felogin.extbase' => true,
            'fluidBasedPageModule' => true,
            'rearrangedRedirectMiddlewares' => true,
            'unifiedPageTranslationHandling' => true,
        ],
        'sitename' => 'TYPO3 Demo',
        'systemMaintainers' => [
            1,
        ],
    ],
];
