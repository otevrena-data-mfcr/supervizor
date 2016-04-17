<?php

namespace Supervizor\Statistics;

use Kdyby\Doctrine\EntityManager;
use Supervizor\Budget\BudgetGroup;
use Supervizor\Budget\BudgetItem;
use Supervizor\Budget\BudgetRepository;
use Supervizor\Invoice\InvoiceItem;

class StatisticsFacade
{
    /** @var EntityManager */
    private $entityManager;

    /** @var BudgetRepository */
    private $budgetRepository;



    /**
     * StatisticsFacade constructor.
     * @param EntityManager $entityManager
     * @param BudgetRepository $budgetRepository
     */
    public function __construct(EntityManager $entityManager, BudgetRepository $budgetRepository)
    {
        $this->entityManager = $entityManager;
        $this->budgetRepository = $budgetRepository;
    }



    public function loadStatistics()
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
            $invoiceItemTotal = $budgetItem->getTotalAmount();

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

        return [$skupiny_pocet, $skupiny_objem, $polozky_objem];
    }
}
