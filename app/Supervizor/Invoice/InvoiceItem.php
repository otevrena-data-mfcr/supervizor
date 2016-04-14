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

namespace Supervizor\Invoice;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;
use Supervizor\Budget\BudgetItem;

/**
 * Class InvoiceItem
 * @package Supervizor
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="invoice_item")
 */
class InvoiceItem extends Nette\Object
{

    use Identifier;

    /**
     * @var Invoice
     * @ORM\ManyToOne(targetEntity="Invoice", inversedBy="invoiceItems")
     * @ORM\JoinColumn(name="invoice_id", referencedColumnName="id")
     */
    private $invoice;

    /**
     * @var BudgetItem
     * @ORM\ManyToOne(targetEntity="\Supervizor\Budget\BudgetItem", inversedBy="invoiceItems")
     * @ORM\JoinColumn(name="budgetitem_id", referencedColumnName="id")
     */
    private $budgetItem;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $amount;

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
     * InvoiceItem description.
     * @param Invoice $invoice
     * @param BudgetItem $budgetItem
     * @param float $amount
     */
    public function __construct(Invoice $invoice, BudgetItem $budgetItem, $amount)
    {
        $this->invoice = $invoice;
        $this->budgetItem = $budgetItem;
        $this->amount = $amount;
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
     * 
     * @return Invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * 
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return BudgetItem
     */
    public function getBudgetItem()
    {
        return $this->budgetItem;
    }

}
