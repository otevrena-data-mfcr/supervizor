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
 * Class Supplier
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="supplier")
 */
class Supplier extends Nette\Object
{
    use Identifier;

    /**
     * @var string
     * @ORM\Column(type="string",length=20,unique=true,nullable=false)
     */
    private $identifier;

    /**
     * @var string
     * @ORM\Column(type="string",length=10,nullable=false)
     */
    private $vatIdentifier;
    
    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=false)
     */
    private $name;

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
     * Supplier constructor.
     * @param string $identifier
     * @param string $vatIdentifier
     * @param string $name
     */
    public function __construct($identifier, $vatIdentifier, $name)
    {
        $this->setIdentifier($identifier);
        $this->setVatIdentifier($vatIdentifier);
        $this->setName($name);
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
     * @param string $identifier
     */
    protected function setIdentifier($identifier)
    {
        $identifier = Nette\Utils\Strings::trim($identifier);
        if (Nette\Utils\Validators::length($identifier) === 0) 
        {
            throw new Nette\InvalidArgumentException('Invalid $identifier value');
        }
        $this->identifier = $identifier;
    }
    
    /**
     * @param string $vatIdentifier
     */
    protected function setVatIdentifier($vatIdentifier)
    {
        $vatIdentifier = Nette\Utils\Strings::trim($vatIdentifier);
        if (Nette\Utils\Validators::length($vatIdentifier) === 0) 
        {
            throw new Nette\InvalidArgumentException('Invalid $vatIdentifier value');
        }
        $this->vatIdentifier = $vatIdentifier;
    }
    
    /**
     * @param string $name
     */
    protected function setName($name)
    {
        $name = Nette\Utils\Strings::trim($name);
        if (Nette\Utils\Strings::length($name) === 0) 
        {
            throw new Nette\InvalidArgumentException('Name cannot be empty');
        }
        $this->name = $name;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
    
    /**
     * @return string
     */
    public function getVatIdentifier()
    {
        return $this->vatIdentifier;
    }
    
}