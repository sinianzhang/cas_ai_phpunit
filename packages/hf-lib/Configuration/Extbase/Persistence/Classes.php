<?php
declare(strict_types = 1);

use Hausformat\Lib\Domain\Model\AbstractEntity;
use Hausformat\Lib\Domain\Model\Category;
use Hausformat\Lib\Domain\Model\Content;
use Hausformat\Lib\Domain\Model\FileReference;
use Hausformat\Lib\Domain\Model\FrontendUser;
use Hausformat\Lib\Domain\Model\FrontendUserGroup;
use Hausformat\Lib\Domain\Model\Pages;

return [

    AbstractEntity::class => [
        'properties' => [
            'l10n_parent' => [
                'fieldName' => 'l10nParent',
            ],
        ]
    ],

    Content::class => [
        'tableName' => 'tt_content',
        'properties' => [
            'contentType' => [
                'fieldName' => 'CType',
            ],
            'colPos' => [
                'fieldName' => 'colPos',
            ],
        ]
    ],

    \TYPO3\CMS\Extbase\Domain\Model\Category::class => [
        'subclasses' => [
            0 => Category::class
        ]
    ],

    Category::class => [
        'tableName' => 'sys_category',
    ],

    Pages::class => [
        'tableName' => 'pages',
        'properties' => [
            'TSconfig' => [
                'fieldName' => 'TSconfig',
            ],
        ]
    ],

    FrontendUser::class => [
        'tableName' => 'fe_users',
        'recordType' => 0,
        'properties' => []
    ],

    FrontendUserGroup::class => [
        'tableName' => 'fe_groups',
        'recordType' => 0,
        'properties' => []
    ],

    FileReference::class => [
        'tableName' => 'sys_file_reference',
        'properties' => [],
    ],

];

