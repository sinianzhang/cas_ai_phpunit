<?php
declare(strict_types=1);

namespace Hausformat\Lib\Service;

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

use Hausformat\Lib\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Service class for sending templated emails in TYPO3 using Fluid templates.
 * Provides methods for setting recipients, senders, subject, and attachments, and supports CC/BCC and HTML email rendering.
 *
 * @group  hf-lib
 * @author .hausformat <entwicklung@hausformat.com>
 */
class TemplateEmailService
{

    /**
     * @var \TYPO3\CMS\Core\View\ViewFactoryInterface
     */
    protected $viewFactory;

    /**
     * @param \TYPO3\CMS\Core\View\ViewFactoryInterface $viewFactory
     *
     * @return void
     */
    public function injectViewFactory(ViewFactoryInterface $viewFactory) {
        $this->viewFactory = $viewFactory;
    }

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     *
     */
    protected $configurationManager;

    /**
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * @param array $recipient recipient of the email in the format ['recipient@domain.tld' => 'Recipient Name']
     * @param array $sender sender of the email in the format ['sender@domain.tld' => 'Sender Name']
     * @param string $subject subject of the email
     * @param string $templateName template name (UpperCamelCase)
     * @param string|null $extensionName
     * @param array $variables variables to be passed to the Fluid view
     * @param array $attachments Attachments to add to the Mail
     * @param array|string $cc
     * @param array|string $bcc
     *
     * @return boolean true on success, otherwise false
     */
    public function sendTemplateEmail(
        array $recipient,
        array $sender,
        string $subject,
        string $templateName,
        ?string $extensionName = null,
        array $variables = [],
        array $attachments = [],
        $cc = [],
        $bcc = []
    )
    {
        $templatePathAndFilename = GeneralUtility::getFileAbsFileName($templateName);

        $sitePath = Environment::getPublicPath();

        if ($templatePathAndFilename == '' || $templatePathAndFilename == $sitePath . '/' . $templateName) {
            $templatePathAndFilename = null;
        }

        if(is_string($extensionName) && strlen($extensionName) > 0) {
            try {
                $extensionFrameworkConfiguration = TypoScriptUtility::getFrameworkConfigurationForExtension($extensionName);
            } catch (\Exception $e) {
                $extensionFrameworkConfiguration = [];
            }
        } else {
            $extensionFrameworkConfiguration = [];
        }

        $data = new ViewFactoryData(
            $extensionFrameworkConfiguration['view']['templateRootPaths'] ?? [],
            $extensionFrameworkConfiguration['view']['partialRootPaths'] ?? [],
            $extensionFrameworkConfiguration['view']['layoutRootPaths'] ?? [],
            $templatePathAndFilename,
        );

        /** @var \TYPO3\CMS\Fluid\View\FluidViewAdapter $emailView */
        $emailView = $this->viewFactory->create($data);

        if ($templatePathAndFilename == '' || $templatePathAndFilename == $sitePath . '/' . $templateName) {
            /** @var \TYPO3Fluid\Fluid\Core\Rendering\RenderingContext $renderingContext */
            $renderingContext = $emailView->getRenderingContext();
            $renderingContext->setControllerName('Email');
            $renderingContext->setControllerAction($templateName);
        }

        $emailView->assignMultiple($variables);
        $emailBody = $emailView->render();

        /** @var MailMessage $message */
        $message = GeneralUtility::makeInstance(MailMessage::class);
        $message->setTo($recipient)
            ->setFrom($sender)
            ->setSubject($subject);

        if ($cc !== []) {
            $message->setCc($cc);
        }
        if ($bcc !== []) {
            $message->setBcc($bcc);
        }

        // Possible attachments here
        foreach ($attachments as $attachment) {
            $message->attachFromPath($attachment);
        }

        // HTML Email
        $message->html($emailBody);


        $message->send();

        return $message->isSent();
    }
}
