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

namespace Supervizor\Budget;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;
use Doctrine\Common\Collections\ArrayCollection;
use Supervizor\Invoice\InvoiceItem;

/**
 * Class BudgetItem
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="budget_item")
 */
class BudgetItem extends Nette\Object
{

    use Identifier;

    /**
     * @var int
     * @ORM\Column(type="integer",nullable=true)
     */
    private $identifier;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=false)
     */
    private $name;

    /**
     * @var BudgetGroup
     * @ORM\ManyToOne(targetEntity="\Supervizor\Budget\BudgetGroup", inversedBy="budgetItems")
     * @ORM\JoinColumn(name="budgetgroup_id", referencedColumnName="id", nullable=true)
     */
    private $budgetGroup;

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
     * @var ArrayCollection|InvoiceItem[]
     * @ORM\OneToMany(targetEntity="\Supervizor\Invoice\InvoiceItem", mappedBy="budgetItem",cascade={"persist"})
     */
    private $invoiceItems;

    /**
     * BudgetItem constructor.
     * @param $identifier
     * @param $name
     * @param BudgetGroup|null $budgetGroup
     */
    public function __construct($identifier, $name, BudgetGroup $budgetGroup = null)
    {
        $this->budgetGroup = $budgetGroup;
        $this->setIdentifier($identifier);
        $this->setName($name);

        $this->invoiceItems = new ArrayCollection();
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
        if (Nette\Utils\Strings::length($identifier) === 0)
        {
            throw new Nette\InvalidArgumentException('Identifier cannot be empty');
        }
        $this->identifier = $identifier;
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

    public function setBudgetGroup(BudgetGroup $budgetGroup)
    {
        $this->budgetGroup = $budgetGroup;
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return InvoiceItem[]|ArrayCollection
     */
    public function getinvoiceItems()
    {
        return $this->invoiceItems;
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return float
     */
    public function getTotalAmount()
    {
        $amount = 0;

        foreach ($this->getinvoiceItems() as $invoiceItem) {
            $amount += $invoiceItem->getAmount();
        }

        return $amount;
    }

}
