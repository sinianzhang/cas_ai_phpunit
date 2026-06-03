<?php

namespace Hausformat\Lib\Service\Interfaces;

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

/**
 * Mail Service Class Interface
 *
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
interface MailServiceInterface
{

    /**
     * calls the template and fills in the data array
     *
     * @param array $data
     * @param string $templateName
     *
     * @return string rendered HTML text
     */
    public function getHtml(array $data, $templateName = 'default');

    /**
     * calls the template and fills in the data array
     *
     * @param array $data
     * @param string $templateName
     *
     * @return string rendered plain text
     */
    public function getPlainText(array $data, $templateName = 'default');

    /**
     * creates a new mail instance
     *
     * @return \TYPO3\CMS\Core\Mail\MailMessage
     */
    public function newMail();

    /**
     * send the mail
     *
     * @param \TYPO3\CMS\Core\Mail\MailMessage $mail
     */
    public function sendMail(\TYPO3\CMS\Core\Mail\MailMessage $mail);

    /**
     * setter for controller
     *
     * @param \Hausformat\Lib\Controller\BaseController $controller
     *
     * @return mixed
     */
    public function setController(\Hausformat\Lib\Controller\BaseController &$controller);

    /**
     * setter for the extension name
     *
     * @param string $name
     */
    public function setExtensionName($name);

    /**
     * inject the controller context
     *
     * @param \TYPO3\CMS\Extbase\Mvc\Request $request
     */
    public function setRequest(\TYPO3\CMS\Extbase\Mvc\Request &$request);

}
