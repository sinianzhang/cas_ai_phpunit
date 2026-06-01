<?php

declare(strict_types=1);

namespace B13\DemoLogin\Service;

/*
 * This file is part of TYPO3 CMS-based extension "demologin" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use TYPO3\CMS\Core\Authentication\AbstractAuthenticationService;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DemoLoginAuthenticationService extends AbstractAuthenticationService
{
    public function getUser()
    {
        if ($this->login['uident'] !== '') {
            // we do this to ignore user password login tries, which are still possible for the b13 user
            return false;
        }
        $userName = $this->login['uname'];
        $userGroup = (int)$this->authInfo['request']->getParsedBody()['userGroup'];

        $userNameSuffix = $this->getUserNameSuffix($userName);

        return $this->createNewUser($userName . $userNameSuffix, $userGroup);
    }

    protected function getUserNameSuffix(string $username): string
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('be_users');

        $usedUsernames = $queryBuilder
            ->select('username')
            ->from('be_users')
            ->where(
                $queryBuilder->expr()->like(
                    'username',
                    $queryBuilder->createNamedParameter($username . '%')
                )
            )
            ->executeQuery()->fetchOne();

        if (empty($usedUsernames)) {
            return '';
        }

        $usernameLength = strlen($username);
        $lastUsername = 0;
        foreach ($usedUsernames as $usedUsername) {
            $lastUsername = max($lastUsername, substr($usedUsername, $usernameLength));
        }

        return (string)($lastUsername + 1);
    }

    protected function createNewUser(string $userName, int $userGroup): array
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('be_users');

        // we only add the required fields
        $connection->insert('be_users', [
            'tstamp' => $GLOBALS['EXEC_TIME'],
            'crdate' => $GLOBALS['EXEC_TIME'],
            'username' => $userName,
            'usergroup' => $userGroup,
            'options' => 3, // options=3 means that both the DB and file mounts should be inherited from the group
        ], [
            'tstamp' => Connection::PARAM_INT,
            'crdate' => Connection::PARAM_INT,
            'username' => Connection::PARAM_STR,
            'usergroup' => Connection::PARAM_STR,
            'options' => Connection::PARAM_INT,
        ]);

        // and return the user for further processing
        $queryBuilder = $connection->createQueryBuilder();
        $user = $queryBuilder
            ->select('*')
            ->from('be_users')
            ->where(
                $queryBuilder->expr()->eq('username', $queryBuilder->createNamedParameter($userName))
            )
            ->executeQuery()
            ->fetchAssociative();
        return is_array($user) ? $user : [];
    }

    public function authUser(array $user): int
    {
        if ($this->login['uident'] !== '') {
            // we do this to ignore user password login tries, which are still possible for the b13 user
            return 100;
        }

        // always login user
        return 200;
    }
}
