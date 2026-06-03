<?php
/** @noinspection PhpUnused */
declare(strict_types=1);

namespace Hausformat\Lib\Controller;

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

use Hausformat\Lib\Domain\Model\FrontendUser;
use Hausformat\Lib\Domain\Repository\BaseRepository;
use Hausformat\Lib\Domain\Repository\FrontendUserRepository;
use Hausformat\Lib\Pagination\AlphabeticalPaginator;
use Hausformat\Lib\Service\GlobalsService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\Exception;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Base Controller with often used functionallity
 *
 * @group hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
abstract class BaseController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @var FrontendUser|null
     */
    protected $feUser;

    /**
     * FeUser Repository
     *
     * @var mixed
     */
    protected $feUserRepository;

    /**
     * @var string
     */
    protected $modelName = 'AbstractEntity';

    /**
     * @var string
     * @api
     */
    protected $namespacesDomainModelObjectNamePattern = '@vendor\@extension\Domain\Model\@controller';

    /**
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    protected ModuleTemplateFactory $moduleTemplateFactory;

    /**
     * @var ModuleTemplate
     */
    protected $moduleTemplate = null;

    /**
     * getter for extension key
     *
     * @return string
     */
    public function getExtensionKey()
    {
        return $this->request->getControllerExtensionKey();
    }

    /**
     * get the current FeUser
     *
     * @return mixed
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception
     */
    public final function getFeUser()
    {
        if (!$this->feUserRepository instanceof \TYPO3\CMS\Extbase\Persistence\Repository) {
            $this->setFeUserRepository();
        }
        if (empty($this->feUser)) {
            $frontendUser = GlobalsService::getInstance()->getFrontendUser();
            if ($frontendUser != null && !empty($frontendUser->user['uid'])) {
                if ($this->feUserRepository instanceof BaseRepository) {
                    $this->feUserRepository->setRespectStoragePage(false);
                }

                $user = $this->feUserRepository->findByUid(intval($frontendUser->user['uid']));
                $this->feUser = $user;
            }
        }

        return $this->feUser;
    }

    /**
     * set the FeUserRepository
     *
     * @param mixed $repository
     *
     * @return bool
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception
     */
    protected function setFeUserRepository($repository = null)
    {
        // string given, try to create repository
        if (!is_object($repository)) {
            if (is_string($repository) && class_exists($repository)) {
                $repository = GeneralUtility::makeInstance($repository);
            } elseif (isset($this->settings['FeUserRepository']) && $this->settings['FeUserRepository'] && class_exists($this->settings['FeUserRepository'])) {
                $repository = GeneralUtility::makeInstance($this->settings['FeUserRepository']);
            } else {
                $repository = GeneralUtility::makeInstance(FrontendUserRepository::class);
            }
        }

        // try to set the repository
        if ($repository instanceof \TYPO3\CMS\Extbase\Persistence\Repository) {
            $this->feUserRepository = $repository;

            return true;
        }

        throw new Exception('feUserRepository must be an instance of \TYPO3\CMS\Extbase\Persistence\Repository. Is [' . get_class($repository) . '] instead',
            1289419709);

    }

    /**
     * persists all changes
     *
     * @return void
     */
    public function persistAll()
    {
        if (!$this->persistenceManager instanceof PersistenceManagerInterface) {
            $this->persistenceManager = GeneralUtility::makeInstance(PersistenceManagerInterface::class);
        }
        $this->persistenceManager->persistAll();
    }



    /**
     * Returns a response object with either the given html string or the current rendered view as content.
     *
     * @param string|null $html
     */
    protected function htmlResponse(?string $html = null): ResponseInterface
    {
        $this->postCallActionMethod($actionResult);
        if($this->moduleTemplate != null) {
            // HF-TODO: Check if there is a Way to assign all Variables previously added to the 'normal' View
            $this->moduleTemplate->assignMultiple([]);
            // HF-TODO: check if this is correct or should we return directly $this->moduleTemplate->renderResponse()
            $moduleHtml = $this->moduleTemplate->render();
            if(trim($moduleHtml) != '') {
                $html = $moduleHtml;
            }
        }

        return $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'text/html; charset=utf-8')
            ->withBody($this->streamFactory->createStream((string)($html ?? $this->view->render())));
    }

    /**
     * Prepares a view for the current action.
     * By default, this method tries to locate a view with a name matching the current action.
     *
     * @return ViewInterface
     */
    protected function resolveView(): ViewInterface
    {
        if (($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
                && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()) {
            $this->moduleTemplateFactory = GeneralUtility::makeInstance(ModuleTemplateFactory::class);
            $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        }
        return parent::resolveView();
    }

    /**
     * this function will be called, if the action method was successfull
     *
     * @param $actionResult
     *
     * @return void
     */
    protected function postCallActionMethod(&$actionResult = null)
    {
        $params = [
            'extensionName' => $this->request->getControllerExtensionName(),
            'controllerName' => $this->request->getControllerName(),
            'actionName' => $this->request->getControllerActionName(),
            'pluginName' => $this->request->getPluginName(),
            'pageUid' => GlobalsService::getInstance()->getFrontendPid(),
            'requestUri' => \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'),
            'settings' => $this->settings,
        ];

        $this->view->assignMultiple($params);

        if($this->moduleTemplate != null) {
            $this->moduleTemplate->assignMultiple($params);
        }

        $this->addOffsetAndLimit();
    }

    /**
     * Method to which a query result can be passed.
     * If this has been configured in the settings, a pagination or an alpha-numeric pagination is created and also attached to the view.
     *
     * @param string                                              $name
     * @param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $queryResult
     * @param                                                     $variableName
     *
     * @return void
     */
    protected function addQueryResultToView(string $name, QueryResultInterface $queryResult, $variableName = '') {

        if(isset($this->settings['pagination']) && $this->settings['pagination'] == 1) {
            $limit = $this->getLimit();
            $offset = 0;
            if($this->request->hasArgument('offset')) {
                $offset = $offset = $this->request->getArgument('offset');
            }
            $currentPage = 1;
            if($offset > 0) {
                $currentPage = $offset/$limit + 1;
            }
            $queryResultPaginatior = new QueryResultPaginator($queryResult, $currentPage, $limit);
            $paginatedItems = $queryResultPaginatior->getPaginatedItems();

            $newName = 'paginated' . ucfirst($name);
            $this->view->assign($newName, $paginatedItems);

            $this->view->assign('pagination' . ucfirst($name), [
                    'paginatedItems' => $paginatedItems,
                    'itemsPerPage' => $limit,
                    'pageCount' => $queryResultPaginatior->getNumberOfPages(),
                    'currentPage' => $queryResultPaginatior->getCurrentPageNumber(),
                    'offset' => $offset,
                ]);
        }

        if(isset($this->settings['alphaPagination']) && $this->settings['alphaPagination'] == 1) {
            $currentPage = 0;
            if($this->request->hasArgument('currentPage')) {
                $currentPage = $this->request->getArgument('currentPage');
            }
            $addAll = false;
            if(isset($this->settings['paginationConfig']['addAll']) && $this->settings['paginationConfig']['addAll']) {
                $addAll = true;
            }
            if($variableName == '' && isset($this->settings['paginationConfig']['variableName']) && $this->settings['paginationConfig']['variableName']) {
                $variableName = $this->settings['paginationConfig']['variableName'];
            }
            if(trim($variableName) == '') {
                $variableName = 'title';
            }
            $alphabeticalPaginatior = new AlphabeticalPaginator($queryResult, $addAll, $currentPage, $variableName);
            $paginatedItems = $alphabeticalPaginatior->getPaginatedItems();

            $newName = 'alphaPaginated' . ucfirst($name);
            $this->view->assign($newName, $paginatedItems);

            $this->view->assign('alphaPagination' . ucfirst($name), [
                'paginatedItems' => $paginatedItems,
                'pageCount' => $alphabeticalPaginatior->getNumberOfPages(),
                'currentPage' => $alphabeticalPaginatior->getCurrentPageNumber(),
                'currentItemsCount' => $alphabeticalPaginatior->getCurrentPageItemsCount(),
                'allItemsCount' => $alphabeticalPaginatior->getTotalItemsCount(),
                'pages' => $alphabeticalPaginatior->getPaginationValues(),
                'propertyName' => $variableName,
            ]);
        }

        $this->view->assign($name, $queryResult);
    }




    /**
     * If required, pass offset and limit to the view.
     *
     * @return void
     */
    protected function addOffsetAndLimit()
    {

        try {
            $offset = $this->request->getArgument('offset');
        } catch (NoSuchArgumentException $e) {
            $offset = 0;
        }

        if ($offset > 0) {
            $this->view->assign('offset', $offset);
        }

        $limit = $this->getLimit();

        if ($limit > 0) {
            $this->view->assign('limit', $limit);
        }
    }

    /**
     * Gets the limit if configured or passed.
     *
     * @return int
     */
    protected function getLimit() {
        $limit = 0;
        if ($this->request->hasArgument('limit')) {
            try {
                $limit = $this->request->getArgument('limit');
            } catch (NoSuchArgumentException $ignored) {
            }
        } elseif (isset($this->settings[$this->getObjectType()]['limit'])) {
            $limit = $this->settings[$this->getObjectType()]['limit'];
        } elseif (isset($this->settings['limit'])) {
            $limit = $this->settings['limit'];
        }
        return $limit;
    }

    /**
     *  Determines the fully qualified view object name.
     *
     * @return array|false|string|string[]
     * @api
     */
    protected function getObjectType()
    {
        $extensionClassName = $this->request->getControllerExtensionName();
        $extensionNamePosition = strpos($extensionClassName, $this->getExtensionName());

        $vendorName = substr(
            $extensionClassName,
            0,
            $extensionNamePosition
        );
        if ($vendorName == null) {
            return false;
        }

        $possibleModelName = str_replace(
            [
                '@vendor',
                '@extension',
                '@controller',
            ],
            [
                $vendorName,
                $this->request->getControllerExtensionName(),
                $this->request->getControllerName(),
            ],
            $this->namespacesDomainModelObjectNamePattern
        );

        return class_exists($possibleModelName) ? $possibleModelName : false;
    }

    /**
     * getter for extension name
     *
     * @return string
     */
    public function getExtensionName()
    {
        return $this->request->getControllerExtensionName();
    }

    /**
     * check if a BeLogin exists. If not, it redirects using the given parameters
     *
     * @param mixed $action
     * @param mixed $controller
     * @param mixed $extension
     * @param mixed $arguments
     * @param mixed $pageUid
     * @param mixed $delay
     * @param mixed $statusCode
     *
     * @return \Psr\Http\Message\ResponseInterface|void
     * @see \TYPO3\CMS\Extbase\Mvc\Controller\AbstractController->redirect for more info about the parameter
     */
    protected final function checkBeLogin(
        $action = null,
        $controller = null,
        $extension = null,
        $arguments = null,
        $pageUid = null,
        $delay = null,
        $statusCode = null
    )
    {
        // redirect, if not logged in
        if (!$this->hasBeLogin()) {
            return $this->redirect($action, $controller, $extension, $arguments, $pageUid, $delay, $statusCode);
        }
    }

    /**
     * check if there is a backend login
     *
     * @return bool
     */
    public final function hasBeLogin()
    {
        return GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('backend.user', 'isLoggedIn', false);
    }

    /**
     * check if a FeLogin exists. If not, it redirects using the given parameters
     *
     * @param mixed $action
     * @param mixed $controller
     * @param mixed $extension
     * @param mixed $arguments
     * @param mixed $pageUid
     * @param mixed $delay
     * @param mixed $statusCode
     *
     * @return \Psr\Http\Message\ResponseInterface|void
     * @see \TYPO3\CMS\Extbase\Mvc\Controller\AbstractController->redirect for more info about the parameter
     */
    protected final function checkFeLogin(
        $action = null,
        $controller = null,
        $extension = null,
        $arguments = null,
        $pageUid = null,
        $delay = null,
        $statusCode = null
    )
    {
        // redirect, if not logged in
        if (!$this->hasFeLogin()) {
            return $this->redirect($action, $controller, $extension, $arguments, $pageUid, $delay, $statusCode);
        }
    }

    /**
     * check if there is a backend login
     *
     * @param bool $useBeLoginAsFeLogin
     *
     * @return bool
     */
    public final function hasFeLogin($useBeLoginAsFeLogin = false)
    {
        $feUserLoggedIn = GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('frontend.user', 'isLoggedIn', false);
        if (!$feUserLoggedIn) {
            if ($useBeLoginAsFeLogin) {
                return $this->hasBeLogin();
            }
        }
        return $feUserLoggedIn;
    }

    /**
     * By default this Methode clears all Caches, but you can Submit a Integer or the Values 'pages', 'all' or 'system' for cleare several caches
     *
     * @param int|string $clearType
     *
     * @return void
     */
    protected function clearCache($clearType = 'all')
    {
        /**
         * @var DataHandler $tce
         */
        $tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(DataHandler::class);
        $tce->admin = true;
        $tce->clear_cacheCmd($clearType);
    }

    /**
     * A template method for displaying custom error flash messages, or to
     * display no flash message at all on errors. Override this to customize
     * the flash message in your action controller.
     *
     * @return string|bool The flash message or FALSE if no flash message should be set
     * @api
     */
    protected function getErrorFlashMessage(): bool|string
    {

        $message = '';
        $allMessages = '';
        $result = $this->arguments->validate();

        if ($result->hasErrors()) {
            $allErrors = $result->getFlattenedErrors();

            if (isset($allErrors[$this->modelName])) {
                $model = $allErrors[$this->modelName];

                /**
                 * @var \TYPO3\CMS\Extbase\Error\Error $errorMessage
                 */
                foreach ($model AS $errorMessage) {
                    if (strpos($allMessages, $errorMessage->getTitle()) === false && $errorMessage->getTitle() !== '') {
                        $this->addFlashMessage($errorMessage->getMessage());
                        $allMessages .= $errorMessage->getTitle() . ' - ';
                        $message = false;
                    }
                }

            }

            return $message;

        } else {
            parent::getErrorFlashMessage();
        }
        return false;
    }

    /**
     * Prepares the controller before executing an action, including object preloading in the frontend
     * and setting repository limits and offsets based on request arguments.
     *
     * @return void
     *
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception
     */
    protected function initializeAction(): void
    {
        parent::initializeAction();

        if ((($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
                && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend())
            && ((isset($this->settings['preloadObjectsInFrontend']) && $this->settings['preloadObjectsInFrontend'])
            || (isset($this->settings[$this->getObjectType()]['preloadObjectsInFrontend']) && $this->settings[$this->getObjectType()]['preloadObjectsInFrontend']))) {
            $this->preloadObjects();
        }

        $this->setFeUserRepository();

        if ($this->request->hasArgument('limit')) {
            $limit = $this->request->getArgument('limit');
            $this->settings[$this->getObjectType()]['limit'] = $limit;
            $myRepo = $this->getObjectRepository();
            if ($myRepo instanceof BaseRepository) {
                $myRepo->setDefaultLimit((int)$limit);
            }
        }
        if ($this->request->hasArgument('offset')) {
            //$this->settings[$this->getObjectType()]['offset'] = $this->request->getArgument('offset');
            $offset = $this->request->getArgument('offset');
            $this->settings[$this->getObjectType()]['offset'] = $offset;
            $myRepo = $this->getObjectRepository();
            if ($myRepo instanceof BaseRepository) {
                $myRepo->setDefaultOffset((int)$offset);
            }
        }

    }

    /**
     *  Preload Objects for correct displaying Translated Objects
     *
     * @return void
     */
    protected function preloadObjects()
    {
        // Preload submited Objects for displaying hidden Objects in BE.

        foreach ($this->arguments AS $key => $value) {
            if ($this->request->hasArgument($key)) {
                $objectUid = $this->request->getArgument($key);
                $repositoryValueName = $key . 'Repository';
                $temp = null;
                $beEditLanguage = 0;
                if ($this->request->hasArgument('beEditLanguage')) {
                    $beEditLanguage = $this->request->getArgument('beEditLanguage');
                }
                if (property_exists(get_class($this), $repositoryValueName)) {

                    $lookForUid = $this->getUidForObject($objectUid);

                    if ($beEditLanguage) {
                        $this->loadObjectByL10NParent($lookForUid, $repositoryValueName, $beEditLanguage);
                    } else {
                        $this->{$repositoryValueName}->findByUid($lookForUid);
                    }
                } else {
                    $objectName = $value->getDataType();

                    $repositoryName = str_replace('Model', 'Repository', $objectName) . 'Repository';
                    /** @var \TYPO3\CMS\Extbase\Persistence\Repository $tempRepository */
                    $tempRepository = GeneralUtility::makeInstance($repositoryName);

                    $lookForUid = $this->getUidForObject($objectUid);

                    if ($beEditLanguage && method_exists($tempRepository, 'findByL10nParentAndLanguage')) {
                        $temp = $tempRepository->findByL10nParentAndLanguage($lookForUid, $beEditLanguage, false, true);
                        if ($temp == null) {
                            $tempRepository->findByUid($lookForUid);
                        }
                    } else {
                        $tempRepository->findByUid($lookForUid);
                    }
                }

            }
        }
        // Preload Objects end
    }

    /**
     * Analyzes the passed data and returns the found ID depending on the type of data.
     *
     * @param array|int|string|object $objectInformation
     *
     * @return string|int
     */
    protected function getUidForObject($objectInformation)
    {
        $lookForUid = null;
        if (is_array($objectInformation)) {
            switch (true) {
                case $objectInformation['localizedUid']:
                    $lookForUid = $objectInformation['localizedUid'];
                    break;
                case isset($objectInformation['__identity']):
                    $lookForUid = $objectInformation['__identity'];
                    break;
                case isset($objectInformation['uid']):
                    $lookForUid = $objectInformation['uid'];
                    break;
                default:
                    break;
            }
        } elseif (is_object($objectInformation)) {
            if ($objectInformation instanceof AbstractEntity) {
                $lookForUid = $objectInformation->getUid();
            }
        } else {
            $lookForUid = $objectInformation;
        }

        return $lookForUid;
    }

    /**
     * The method tries to load the correct object for a UID and a language ID.
     *
     * @param     $l10nParentUid
     * @param     $repositoryValueName
     * @param int $language
     *
     * @SuppressWarnings(unused)
     */
    protected function loadObjectByL10NParent($l10nParentUid, $repositoryValueName, $language = 0)
    {
        if (property_exists(get_class($this), $repositoryValueName)) {
            $repository = $this->{$repositoryValueName};
            if ($repository instanceof BaseRepository) {
                /**
                 * @var \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $temp
                 */
                $temp = $repository->findByL10nParentAndLanguage($l10nParentUid, $language, false);
                if ($temp == null) {
                    /** @noinspection PhpUnusedLocalVariableInspection */
                    $temp = $repository->findByUid($l10nParentUid);
                }
            }
        }
    }

    /**
     * Generic method which creates and/or returns the corresponding repository based on the controller name.
     *
     * @return \TYPO3\CMS\Extbase\Persistence\Repository|NULL
     * @api
     */
    protected function getObjectRepository()
    {
        $repository = null;
        $objectName = lcfirst($this->request->getControllerName());

        $controllerObjectName = $this->request->getControllerObjectName();

        if (property_exists('\\' . $controllerObjectName, $objectName . 'Repository')) {
            $propertyName = $objectName . 'Repository';
            $repository = &$this->{$propertyName};
        }

        if ($repository === null) {
            $reponame = ClassNamingUtility::translateModelNameToRepositoryName($this->getObjectType());

            if (class_exists($reponame)) {
                $repository = GeneralUtility::makeInstance($reponame);
            }
        }

        if ($repository instanceof Repository) {
            return $repository;
        }

        return null;
    }

}
