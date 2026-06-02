<?php

namespace Hausformat\ViewHelpers\ViewHelpers\Get;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Returns a field value from some table by uid.
 *
 * This ViewHelper is designed to work with the Container Extension. It returns the given field value of the parent object.
 * If the value doesn't exist or the parent object is not a container, a string 'NaN' will be returned.
 *
 * ## Examples
 *
 *     <hf:get.contentValue uid="{elementUid}" field="CType" /> == hf-column-container
 *
 *     <hf:get.contentValue uid="{noneExistingUid}" field="CType" default="helloWorld" /> == helloWorld
 *
 * @group  hf-viewhelpers
 * @author .hausformat <entwicklung@hausformat.com>
 */
class ContentValueViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('uid', 'int', 'UID of an object', true);
        $this->registerArgument('field', 'string', 'Name of the field', false, 'CType');
        $this->registerArgument('default', 'string', 'What to return on NaN return instead of NaN', false, 'NaN');
        $this->registerArgument('table', 'string', 'Table name', false, 'tt_content');
    }

    public function render(): int|string
    {
        $arguments = $this->arguments;
        $parentUid = intval($arguments[ 'uid' ]);
        $field = $arguments[ 'field' ];
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($arguments[ 'table' ]);

        $where = [
            $queryBuilder->expr()->eq('deleted', 0),
            $queryBuilder->expr()->eq('hidden', 0),
            $queryBuilder->expr()->eq('uid', $parentUid),
        ];
        try {
            $result = $queryBuilder
                ->select($field)
                ->from($arguments[ 'table' ])
                ->where(...$where)
                ->executeQuery()
                ->fetchFirstColumn();
            if (count($result)) {
                return (string) $result[ 0 ];
            }
        } catch (\Doctrine\DBAL\Exception $e) {
        }

        return $arguments[ 'default' ];
    }
}
