<?php declare(strict_types=1);

namespace Hausformat\Lib\UserFunctions;


use TYPO3\CMS\Backend\Utility\BackendUtility;

class ShowLayoutDisplayCondition
{
    /**
     * Returns true if the element is not inside a container
     *
     * To invert the result, use the condition `...:!=:0` (!= does not work)
     *
     * @param array $data
     *
     * @return bool
     */
    public function showLayout(array $data): bool
    {
        $showLayout = $this->isParentNotContainer($data[ 'record']);
        $condition = $data['conditionParameters'];
        if (isset($condition[0]) && $condition[0] === '=') {
            if (isset($condition[1]) && $condition[1] === '0') {
                return !$showLayout;
            }
        }
        return $showLayout;
    }

    private function isParentNotContainer(array $row): bool
    {
        if (empty($row['tx_container_parent']) || $row['tx_container_parent'][0] == "0") {
            return true;
        }
        $parentRow = BackendUtility::getRecord('tt_content', $row['tx_container_parent'][0]);
        if (is_null($parentRow['CType'])) {
            return true;
        }
        $isGridContainer = str_ends_with($parentRow['CType'], 'column-container') && $parentRow['CType'] !== 'hf-column-container';
        if ($parentRow['layout'] > 12 && !$isGridContainer) {
            return true;
        }
        return false;
    }
}
