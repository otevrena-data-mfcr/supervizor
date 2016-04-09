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

use App\Model\Entities\BudgetGroup;
use App\Model\Entities\BudgetItem;
use Kdyby\Doctrine\EntityManager;

class BudgetRepository
{
    /** @var \Kdyby\Doctrine\EntityRepository */
    private $budgetGroupRepository;
    
    /** @var \Kdyby\Doctrine\BudgetItem */
    private $budgetItemRepository;
    
    public function __construct(EntityManager $entityManager)
    {
        $this->budgetGroupRepository = $entityManager->getRepository(BudgetGroup::class);
        $this->budgetItemRepository = $entityManager->getRepository(BudgetItem::class);
    }
    
    /**
     * @return \Kdyby\Doctrine\EntityRepository
     */
    public function getBudgetGroupRepository()
    {
        return $this->budgetGroupRepository;
    }
    
    
    public function findByIdentifier($identifier)
    {
        return $this->budgetItemRepository->findOneBy(['identifier' => $identifier]);
    }
}