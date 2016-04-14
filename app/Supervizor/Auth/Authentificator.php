<?php

/*
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301  USA
 */

namespace Supervizor\Auth;

use Nette;
use Nette\Security\AuthenticationException;
use Nette\Security\IIdentity;

class Authenticator extends Nette\Object implements Nette\Security\IAuthenticator
{

    /** @var PasswordManager */
    private $passwordManager;

    /** @var UserRepository */
    private $userRepository;

    /** @var string */
    private $namespace;

    /**
     * Authenticator constructor.
     * @param PasswordManager $passwordManager
     * @param UserRepository $userRepository
     */
    public function __construct(PasswordManager $passwordManager, UserRepository $userRepository)
    {
        $this->passwordManager = $passwordManager;
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace ? : null;
    }

    /**
     * Performs an authentication against e.g. database.
     * and returns IIdentity on success or throws AuthenticationException
     * @param array $credentials
     * @return IIdentity
     * @throws AuthenticationException
     */
    function authenticate(array $credentials)
    {
        list ($email, $password) = $credentials;

        $criteria = ['email' => $email];

        if ($this->namespace)
            $criteria['namespace'] = $this->namespace;

        /** @var Entities\User|null $user */
        $user = $this->userRepository->getUserRepository()->findOneBy($criteria);

        if (!$user)
        {
            throw new AuthenticationException('User not found', self::IDENTITY_NOT_FOUND);
        }

        $verifyPassword = $user->verifyPassword($password, function($password, $hash)
        {
            return $this->passwordManager->verify($password, $hash);
        });
        if (!$verifyPassword)
        {
            throw new AuthenticationException('Invalid credentials', self::INVALID_CREDENTIAL);
        }

        // Entity User implements IIdentity - can return as User Identity
        return $user;
    }

}
