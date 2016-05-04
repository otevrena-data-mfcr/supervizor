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
use Doctrine\Common\Collections\ArrayCollection;
use Nette;
use Supervizor\Import\Import;
use Supervizor\Supplies\Supplier;

/**
 * Class Invoice
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="invoice")
 */
class Invoice extends Nette\Object
{

    use Identifier;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=false)
     */
    private $identifier;

    /**
     * @var Supplier
     * @ORM\ManyToOne(targetEntity="\Supervizor\Supplies\Supplier", inversedBy="invoices")
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
     */
    private $supplier;

    /**
     * @var Import
     * @ORM\ManyToOne(targetEntity="\Supervizor\Import\Import", inversedBy="invoices", cascade={"persist"})
     * @ORM\JoinColumn(name="import_id", referencedColumnName="id")
     */
    private $import;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=false)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=false)
     */
    private $distinction;

    /**
     * @var bool
     * @ORM\Column(type="boolean",nullable=false)
     */
    private $vatRecord;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $amount;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $amountWithoutVat;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $amountOriginal;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $amountPaid;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=14, scale=2)
     */
    private $amountPaidOriginal;

    /**
     * @var string
     * @ORM\Column(type="string",length=3,nullable=false)
     */
    private $currency;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime",nullable=false)
     */
    private $issued;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime",nullable=false)
     */
    private $received;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime",nullable=false)
     */
    private $maturity;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime",nullable=false)
     */
    private $paid;

    /**
     * @var string
     * @ORM\Column(type="string",length=6000,nullable=false)
     */
    private $description;

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
     * @ORM\OneToMany(targetEntity="\Supervizor\Invoice\InvoiceItem", mappedBy="invoice",cascade={"persist"})
     */
    private $invoiceItems;

    /**
     * Invoice constructor.
     * @param $identifier
     * @param $type
     * @param $distinction
     * @param $vatRecord
     * @param $amount
     * @param $amountWithoutVat
     * @param $amountOriginal
     * @param $amountPaid
     * @param $amountPaidOriginal
     * @param $currency
     * @param $issued
     * @param $received
     * @param $maturity
     * @param $paid
     * @param $description
     * @param Supplier|null $supplier
     */
    public function __construct(
        Import $import,
        $identifier,
        $type,
        $distinction,
        $vatRecord,
        $amount,
        $amountWithoutVat,
        $amountOriginal,
        $amountPaid,
        $amountPaidOriginal,
        $currency,
        $issued,
        $received,
        $maturity,
        $paid,
        $description,
        Supplier $supplier = null
    )
    {
        $this->import = $import;
        $this->supplier = $supplier;
        $this->identifier = $identifier;
        $this->type = $type;
        $this->distinction = $distinction;
        $this->vatRecord = $vatRecord;
        $this->amount = $amount;
        $this->amountWithoutVat = $amountWithoutVat;
        $this->amountOriginal = $amountOriginal;
        $this->amountPaid = $amountPaid;
        $this->amountPaidOriginal = $amountPaidOriginal;
        $this->currency = $currency;
        $this->issued = $issued;
        $this->received = $received;
        $this->maturity = $maturity;
        $this->paid = $paid;
        $this->description = $description;

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
     * @param Import $import
     */
    public function setImport(Import $import)
    {
        $this->import = $import;
    }

    /**
     * @return InvoiceItem[]|ArrayCollection
     */
    public function getInvoiceItems()
    {
        return $this->invoiceItems;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getPaid()
    {
        return $this->paid;
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
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getDistinction()
    {
        return $this->distinction;
    }

    /**
     * @return bool
     */
    public function getVatRecord()
    {
        return $this->vatRecord;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return float
     */
    public function getAmountWithoutVat()
    {
        return $this->amountWithoutVat;
    }

    /**
     * @return float
     */
    public function getAmountOriginal()
    {
        return $this->amountOriginal;
    }

    /**
     * @return float
     */
    public function getAmountPaidOriginal()
    {
        return $this->amountPaidOriginal;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getIssued()
    {
        return $this->issued;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getReceived()
    {
        return $this->received;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getMaturity()
    {
        return $this->maturity;
    }

    /**
     * @return float
     */
    public function getAmountPaid()
    {
        return $this->amountPaid;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function getSupplier()
    {
        return $this->supplier;
    }
}
