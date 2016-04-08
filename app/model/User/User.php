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

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;

/**
 * Class User
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="user")
 */
class User extends Nette\Object implements Nette\Security\IIdentity
{
    use Identifier;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,unique=true,nullable=false)
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string",length=100,nullable=false)
     */
    private $password;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime",nullable=false)
     */
    private $created;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime",nullable=false)
     */
    private $updated;

    /**
     * User constructor.
     * @param string $email
     * @param string $password
     * @param callable $passwordHashCallable
     */
    public function __construct($email, $password, callable $passwordHashCallable)
    {
        $this->setEmail($email);
        $this->setPassword($password, $passwordHashCallable);
    }
    
    /**
     * Gets triggered only on insert
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->created = $this->updated = new \DateTime();
    }

    /**
     * Gets triggered every time on update
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updated = new \DateTime();
    }

    /**
     * @param string $email
     */
    protected function setEmail($email)
    {
        $email = Nette\Utils\Strings::trim(Nette\Utils\Strings::lower($email));
        if (!Nette\Utils\Validators::isEmail($email)) 
        {
            throw new Nette\InvalidArgumentException('Invalid $email value');
        }
        $this->email = $email;
    }
    
    /**
     * @param string $password
     * @param callable $hash
     */
    protected function setPassword($password, callable $hash)
    {
        $password = Nette\Utils\Strings::trim($password);
        if (Nette\Utils\Strings::length($password) === 0) 
        {
            throw new Nette\InvalidArgumentException('Password cannot be empty');
        }
        $this->password = $hash($password);
    }

    /**
     * @param string $password
     * @param callable $hash
     */
    public function changePassword($password, callable $hash)
    {
        $this->setPassword($password, $hash);
    }

    /**
     * @param string $password
     * @param callable $verifyPassword
     * @return bool
     */
    public function verifyPassword($password, callable $verifyPassword)
    {
        return $verifyPassword($password, $this->password);
    }
    
    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * Returns a list of roles that the user is a member of.
     * @return array
     */
    public function getRoles()
    {
        return [];
    }
}