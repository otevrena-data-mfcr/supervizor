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
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Import
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="import_group")
 */
class ImportGroup extends Nette\Object
{

    use Identifier;

    /**
     * @var ArrayCollection|Import[]
     * @ORM\OneToMany(targetEntity="Import", mappedBy="importGroup",cascade={"persist"})
     */
    private $imports;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=false)
     */
    private $slug;

    /**
     * @var bool
     * @ORM\Column(type="boolean",nullable=false)
     */
    private $isDefault;

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
     * ImportGroup constructor.
     * @param $name
     * @param $slug
     * @param $isDefault
     */
    public function __construct($name, $slug, $isDefault)
    {
        $this->setName($name);
        $this->setSlug($slug);
        $this->setIsDefault($isDefault);

        $this->imports = new ArrayCollection();
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
    protected function setName($name)
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
    protected function setSlug($slug)
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
    protected function setIsDefault($isDefault)
    {
        if (!is_bool($isDefault)) {
            throw new Nette\InvalidArgumentException('Invalid $isDefault value');
        }
        $this->isDefault = $isDefault;
    }
}
