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

namespace Supervizor\Import;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;
use Doctrine\Common\Collections\ArrayCollection;
use Supervizor\Invoice\Invoice;

/**
 * Class Import
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="import")
 */
class Import extends Nette\Object
{

    use Identifier;

    /**
     * @var ImportGroup
     * @ORM\ManyToOne(targetEntity="\Supervizor\Import\ImportGroup", inversedBy="imports", cascade={"persist"})
     * @ORM\JoinColumn(name="importgroup_id", referencedColumnName="id")
     */
    private $importGroup;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=false)
     */
    private $name;

    /**
     * @var bool
     * @ORM\Column(type="boolean",nullable=false)
     */
    private $isDefault;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=false)
     */
    private $slug;

    /**
     * @var string
     * @ORM\Column(type="string",length=6000,nullable=false)
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="string",length=6000,nullable=false)
     */
    private $homepage;

    /**
     * @var ArrayCollection|Invoice[]
     * @ORM\OneToMany(targetEntity="\Supervizor\Invoice\Invoice", mappedBy="import", cascade={"persist"})
     */
    private $invoices;

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

    
    public function __construct(ImportGroup $importGroup, $name, $slug, $description, $homepage, $isDefault)
    {
        $this->setImportGroup($importGroup);
        $this->setName($name);
        $this->setSlug($slug);
        $this->setIsDefault($isDefault);
        $this->setHomepage($homepage);
        $this->setDescription($description);
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
     * @param string $name
     */
    public function setName($name)
    {
        $name = Nette\Utils\Strings::trim($name);
        if (!$name) {
            throw new Nette\InvalidArgumentException('Invalid $name value');
        }
        $this->name = $name;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $slug = Nette\Utils\Strings::trim($slug);
        if (!$slug) {
            throw new Nette\InvalidArgumentException('Invalid $slug value');
        }
        $this->slug = $slug;
    }

    /**
     * @param bool $isDefault
     */
    public function setIsDefault($isDefault)
    {
        if (!is_bool($isDefault)) {
            throw new Nette\InvalidArgumentException('Invalid $isDefault value');
        }
        $this->isDefault = $isDefault;
    }

    /**
     * @param $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param $homepage
     */
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;
    }

    /**
     * @param ImportGroup $setImportGroup
     */
    public function setImportGroup(ImportGroup $importGroup)
    {
        $this->importGroup = $importGroup;
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
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @return string
     */
    public function getHomepage()
    {
        return $this->homepage;
    }
}
