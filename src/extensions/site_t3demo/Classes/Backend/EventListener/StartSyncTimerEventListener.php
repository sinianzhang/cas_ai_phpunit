<?php

declare(strict_types=1);

namespace B13\SiteT3demo\Backend\EventListener;

/*
 * This file is part of TYPO3 CMS-extension site_t3demo by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use B13\SiteT3demo\RegistryInterface;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Authentication\Event\AfterUserLoggedInEvent;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Registry;

final readonly class StartSyncTimerEventListener
{
    public function __construct(
        private Registry $registry,
    ) {}

    #[AsEventListener(identifier: 'site-t3demo/backend/after-user-login-start-sync-timer')]
    public function __invoke(AfterUserLoggedInEvent $event): void
    {
        if ((string)Environment::getContext() === 'Production') {
            $date = new \DateTime();
            $counter = $this->registry->get(RegistryInterface::NAMESPACE, RegistryInterface::KEY);
            if ($counter === null) {
                $date->modify('+30 minutes');
                $this->registry->set(RegistryInterface::NAMESPACE, RegistryInterface::KEY, $date->getTimestamp());
            }
        }
    }
}
