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

namespace Supervizor\Console\Commands\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Supervizor\Security\PasswordManager;
use Supervizor\Security\User;

class UsersFixtures extends AbstractFixture implements OrderedFixtureInterface
{

    /** @var PasswordManager */
    private $passwordManager;

    /**
     * UsersFixtures constructor.
     */
    public function __construct()
    {
        $this->passwordManager = new PasswordManager();
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $users = [];
        $users['adam.schubert@sg1-game.net'] = [
            'password' => '123456',
            'firstName' => 'Adam',
            'lastName' => 'Schubert'
        ];

        foreach ($users AS $email => $data)
        {
            $user = new User($email, $data['password'], function($password)
            {
                return $this->passwordManager->hash($password);
            });

            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 2;
    }

}
