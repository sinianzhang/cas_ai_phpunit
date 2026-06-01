<?php
namespace Hausformat\ViewHelpers\ViewHelpers\Widget;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Hausformat\Lib\Pagination\AlphabeticalPaginator;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * This ViewHelper renders a Pagination of objects.
 *
 * ## Examples
 *
 *     <f:widget.paginate objects="{blogs}" as="paginatedBlogs">
 *       // use {paginatedBlogs} as you used {blogs} before, most certainly inside
 *       // a <f:for> loop.
 *     </f:widget.paginate>
 *
 *     <f:widget.paginate objects="{blogs}" as="paginatedBlogs" configuration="{itemsPerPage: 5, insertAbove: 1, insertBelow: 0}">
 *       // use {paginatedBlogs} as you used {blogs} before, most certainly inside
 *       // a <f:for> loop.
 *     </f:widget.paginate>
 *
 * ## Performance characteristics
 *
 * In the above examples, it looks like {blogs} contains all Blog objects, thus
 * you might wonder if all objects were fetched from the database.
 * However, the blogs are NOT fetched from the database until you actually use them,
 * so the paginate ViewHelper will adjust the query sent to the database and receive
 * only the small subset of objects.
 * So, there is no negative performance overhead in using the Paginate Widget.
 *
 * @group hf-viewhelpers
 * @author .hausformat <entwicklung@hausformat.com>
 */
class PaginateAlphabeticalViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('objects', \TYPO3\CMS\Extbase\Persistence\QueryResultInterface::class, '', true);
        $this->registerArgument('as', 'string', '', true);
        $this->registerArgument('searchProperty', 'string', '', false, 'name');
        $this->registerArgument('configuration', 'array', '', false, ['insertAbove' => false, 'insertBelow' => true]);
        $this->registerArgument('addAll', 'boolean', '', false, false);
        $this->registerArgument('allLabel', 'string', '', false, 'LLL:EXT:hf_view_helpers/Resources/Private/Language/locallang.xlf:show.all');
        $this->registerArgument('dontLinkEmpty', 'string', 'Allowed Values: dontLink, remove, default is empty (so it links all)', false, false);
        $this->registerArgument('initializePage', 'int', '', false, null);
    }

    public function render(): string
    {
        $alphabeticalPaginator = new AlphabeticalPaginator($this->arguments['objects'], $this->arguments['addAll'], $this->arguments['initializePage'], $this->arguments['searchProperty']);
        $paginatedItems = $alphabeticalPaginator->getPaginatedItems();

        $name = $this->arguments['as'];

        $renderingContext = $this->renderingContext;
        $container = $renderingContext->getVariableProvider();
        $content = '';

        $container->add('pagination', self::buildPagination($this->arguments, $alphabeticalPaginator));
        $container->add('configuration', $this->arguments['configuration']);

        $container->add(
            'contentArguments',
            [
                $name => $paginatedItems,
            ]
        );

        $content .= $this->renderChildren();

        $container->remove('contentArguments');
        $container->remove('pagination');
        $container->remove('configuration');

        return $content;
    }


    /**
     *
     * @param array $arguments
     * @param AlphabeticalPaginator $alphabeticalPaginator
     * @return array
     */
    protected static function buildPagination(array $arguments, AlphabeticalPaginator $alphabeticalPaginator): array
    {
        $pages = [];
        $numberOfPages = count($alphabeticalPaginator->getPaginationValues());
        $currentPage = $arguments['initializePage'];
        $pagesWithResults = [];

        $nextPage = 1;
        $nextPageWithResults = 1;
        $lastPage = 1;
        $lastPageWithResults = 1;


        if ($arguments['addAll']) {
            $label = $arguments['allLabel'];
            $amount = -1;
            if (str_starts_with($label, 'LLL:')) {
                $label = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($label);
            }
            if ($arguments['dontLinkEmpty'] !== '') {
                $amount = $alphabeticalPaginator->getTotalItemsCount();
            }
            if ($amount > 0) {
                $pagesWithResults[] = -1;
            }
            $lastPage = -1;
            $lastPageWithResults = -1;

            $pages[] = [
                'label' => $label,
                'number' => -1,
                'isCurrent' => (-1 === $currentPage),
                'results' => $amount,
            ];
        }

        $pagesWithResults = $alphabeticalPaginator->getResultsPerPage();

        $i = 1;
        foreach ($pagesWithResults as $pageWithResult => $amount) {
            $pages[] = [
                'label' => $pageWithResult,
                'number' => $i,
                'isCurrent' => ($i === $currentPage),
                'results' => $amount,
            ];
            if($i < $currentPage) {
                $lastPage = $i;
                if($amount > 0) {
                    $lastPageWithResults = $i;
                }
            }
            if($i > $currentPage && $nextPage <= $currentPage) {
                $nextPage = $i;
                if($amount > 0 && $nextPageWithResults <= $currentPage) {
                    $nextPageWithResults = $i;
                }
            }

            $i++;
        }

        return [
            'pages' => $pages,
            'current' => $currentPage,
            'numberOfPages' => $numberOfPages,
            'emptyLinkType' => $arguments['dontLinkEmpty'],
            'nextPage' => $nextPage,
            'nextResultPage' => $nextPageWithResults,
            'previousPage' => $lastPage,
            'previousResultPage' => $lastPageWithResults,
        ];
    }
}
