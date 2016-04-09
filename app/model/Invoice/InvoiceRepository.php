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

namespace App\Model\Repository;

use App\Model\Entities\Invoice;
use App\Model\Entities\InvoiceItem;
use App\Model\Entities\BudgetGroup;
use Kdyby\Doctrine\EntityManager;

class InvoiceRepository
{

    /** @var \Kdyby\Doctrine\EntityRepository */
    private $invoiceRepository;

    /** @var \Kdyby\Doctrine\EntityRepository */
    private $budgetGroupRepository;

    /** @var \Kdyby\Doctrine\EntityRepository */
    private $invoiceItemRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->invoiceRepository = $entityManager->getRepository(Invoice::class);
        $this->budgetGroupRepository = $entityManager->getRepository(BudgetGroup::class);
        $this->invoiceItemRepository = $entityManager->getRepository(InvoiceItem::class);
    }

    public function findItemByInvoiceAndBudgetItem($budgetItem, $invoice)
    {
        return $this->invoiceItemRepository->findOneBy(['budgetItem' => $budgetItem, 'invoice' => $invoice]);
    }

    public function findByIdentifier($identifier)
    {
        return $this->invoiceRepository->findOneBy(['identifier' => $identifier]);
    }

    public function getLastUpdated()
    {
        return $this->invoiceRepository->findOneBy([], ['updated' => 'DESC']);
    }

}
