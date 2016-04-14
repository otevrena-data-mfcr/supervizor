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

use Kdyby\Doctrine\EntityManager;
use Supervizor\Budget\BudgetGroup;
use Supervizor\Budget\BudgetItem;
use Supervizor\Budget\BudgetRepository;
use Supervizor\Invoice\InvoiceItem;
use Supervizor\Invoice\InvoiceRepository;

/**
 * Class StatisticsPresenter
 */
class StatisticsPresenter extends Presenter
{
    /** @var BudgetRepository @inject */
    public $budgetRepository;

    /** @var InvoiceRepository @inject */
    public $invoiceRepository;

    /** @var EntityManager @inject */
    public $entityManager;

    public function renderLoadData()
    {
        $invoiceItemRepository = $this->entityManager->getRepository(InvoiceItem::class);
        $budgetItemRepository = $this->entityManager->getRepository(BudgetItem::class);

        $skupiny_pocet = [
            [
                "Skupina",
                "Počet faktur"
            ]
        ];

        $skupiny_objem = [
            [
                "Skupina",
                "Objem v mil. Kč"
            ]
        ];

        $pocet = $objem = 0;

        /** @var BudgetGroup $budgetGroup */
        foreach ($this->budgetRepository->getBudgetGroupRepository()->findAll() AS $budgetGroup) {
            $invoiceItems = 0;
            $invoiceItemsAmount = 0;
            foreach ($budgetGroup->getBudgetItems() AS $budgetItem) {
                $invoiceItems += $budgetItem->getinvoiceItems()->count();
                foreach ($budgetItem->getinvoiceItems() AS $invoiceItem) {
                    $invoiceItemsAmount += $invoiceItem->getAmount();
                }
            }

            $skupiny_pocet[] = [
                0 => $budgetGroup->getName(),
                1 => $invoiceItems
            ];

            $skupiny_objem[] = array(
                0 => $budgetGroup->getName(),
                1 => max(round($invoiceItemsAmount / 1000000, 1), 0)
            );

            $objem += $invoiceItemsAmount;
            $pocet += $invoiceItems;
        }

        $skupiny_pocet[] = array(
            0 => "Bez rozpočtové položky",
            1 => $invoiceItemRepository->countBy() - $pocet
        );

        /** @var InvoiceItem $invoicesItem */
        $totalInvoicesItemsAmount = 0;
        foreach ($invoiceItemRepository->findAll() AS $invoicesItem) {
            $totalInvoicesItemsAmount += $invoicesItem->getAmount();
        }

        $skupiny_objem[] = array(
            0 => "Bez rozpočtové položky",
            1 => round(($totalInvoicesItemsAmount - $objem) / 1000000, 1)
        );

        $polozky_objem = array(array("Rozpočtová položka", "Objem v mil. Kč"));
        $objem = 0;

        /** @var BudgetItem $budgetItem */
        foreach ($budgetItemRepository->findAll() AS $budgetItem) {
            $invoiceItemTotal = 0;
            foreach ($budgetItem->getinvoiceItems() AS $invoiceItem) {
                $invoiceItemTotal += $invoiceItem->getAmount();
            }

            $polozky_objem[] = array(
                0 => $budgetItem->getIdentifier() . "-" . $budgetItem->getName(),
                1 => max(round($invoiceItemTotal / 1000000, 1), 0)
            );
            $objem += $invoiceItemTotal;
        }

        $polozky_objem[] = array(
            0 => "Bez rozpočtové položky",
            1 => round(($totalInvoicesItemsAmount - $objem) / 1000000, 1)
        );

        $this->payload->skupiny_pocet = $skupiny_pocet;
        $this->payload->skupiny_objem = $skupiny_objem;
        $this->payload->polozky_objem = $polozky_objem;

        $this->sendPayload();
    }

    public function renderDefault()
    {
        $this->template->title = 'Statistiky';
    }
}
