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
    
    public function getBudgetGroups($budgetGroupId = null)
    {
        //$this->budgetGroupRepository->findBy($criteria, $orderBy, $limit, $offset)
        $qb = $this->budgetGroupRepository->createQueryBuilder('bg');
        $qb->select('bg'); //.id, bg.name, bg.description, bg.x, bg.y, bg.color
            //->from('BudgetGroup', 'bg')
            /*->where('u.id = ?1')
            ->orderBy('u.name', 'ASC');*/
        
        //$qb->sum('BudgetItem.InvoiceItem.amount');
        
        if ($budgetGroupId)
        {
            $qb->where('bg.id = :identifier')->setParameter('identifier', $budgetGroupId);
        }
        $a = $qb->getQuery();
        //dump($a->getArrayResult());
        
        
        
        $s = $this->budgetGroupRepository->findBy([]);
        foreach($s AS $u)
        {
            dump($u);
            foreach($u->getBudgetItems() AS $i)
            {
                dump($i);
            }
        }
        
        exit();
        
                /*
        $skupiny = $db->skupina()->select("skupina.id, skupina.nazev_st AS nazev, skupina.popis_tx AS popis, skupina.x,skupina.y,skupina.barva, SUM(COALESCE(skupina_polozka:polozka.faktura_polozka:castka_am,0)) as objem, COUNT(1) as pocet,MAX(UNIX_TIMESTAMP(skupina_polozka:polozka.faktura_polozka:faktura.uhrazeno_dt)) AS max_uhrazeno_udt,MIN(UNIX_TIMESTAMP(skupina_polozka:polozka.faktura_polozka:faktura.uhrazeno_dt)) AS min_uhrazeno_udt")->group("skupina_id");
        if(@$_GET["skupina"]) $skupiny->where("skupina_id",@$_GET["skupina"]);
        $skupiny->order("objem DESC");*/

    }
    
    public function findItemByInvoiceAndBudgetItem($budgetItem, $invoice)
    {
        return $this->invoiceItemRepository->findOneBy(['budgetItem' => $budgetItem, 'invoice' => $invoice]);
    }
    
    public function findByIdentifier($identifier)
    {
        return $this->invoiceRepository->findOneBy(['identifier' => $identifier]);
    }
}