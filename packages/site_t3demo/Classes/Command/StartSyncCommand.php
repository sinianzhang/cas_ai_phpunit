<?php

declare(strict_types=1);

namespace B13\SiteT3demo\Command;

/*
 * This file is part of TYPO3 CMS-extension site_t3demo by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use B13\ContentSync\Command\JobCreatorCommand;
use B13\ContentSync\Command\RunnerCommand;
use B13\SiteT3demo\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class StartSyncCommand extends Command
{
    protected function configure(): void
    {
        $this->setDescription('Checks if the content sync timer is done and starts the sync accordingly.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = new \DateTime();
        $registry = GeneralUtility::makeInstance(Registry::class);
        $counter = $registry->get(RegistryInterface::NAMESPACE, RegistryInterface::KEY);

        if ($counter === null || $counter > $date->getTimestamp()) {
            return 0;
        }

        $output->writeln('Starting content sync.');

        $input = new ArrayInput([]);
        GeneralUtility::makeInstance(JobCreatorCommand::class)->run($input, $output);

        $registry->remove(RegistryInterface::NAMESPACE, RegistryInterface::KEY);

        $input = new ArrayInput([]);
        GeneralUtility::makeInstance(RunnerCommand::class)->run($input, $output);
        return 0;
    }
}
