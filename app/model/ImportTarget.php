<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

use App\Model\Repository\InvoiceRepository;
use App\Model\Repository\SupplierRepository;
use App\Model\Entities\Supplier;
use App\Model\Entities\BudgetItem;
use App\Model\Entities\Invoice;
use App\Model\Entities\InvoiceItem;
use Kdyby\Doctrine\EntityManager;
use App\Model\Repository\BudgetRepository;

/**
 * Description of ImportTarget
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
class ImportTarget implements \Extensions\Importer\IImportTarget
{

    /** @var InvoiceRepository @inject */
    public $invoiceRepository;

    /** @var SupplierRepository @inject */
    public $supplierRepository;

    /** @var BudgetRepository @inject */
    public $budgetRepository;

    /** @var EntityManager @inject */
    public $entityManager;

    /**
     * 
     * @param type $identifier
     * @param type $supplierCompanyIdentifier
     * @param type $name
     * @return Supplier
     */
    private function createSupplier($identifier, $supplierCompanyIdentifier, $name)
    {
        $foundSupplier = $this->supplierRepository->findByIdentifier($identifier);
        if ($foundSupplier)
        {
            return $foundSupplier;
        }

        $supplier = new Supplier($identifier, $supplierCompanyIdentifier, $name);
        $this->entityManager->persist($supplier);
        return $supplier;
    }

    /**
     * 
     * @param type $identifier
     * @param type $name
     * @return BudgetItem
     */
    private function createBudgetItem($identifier, $name)
    {
        $foundBudgetItem = $this->budgetRepository->findByIdentifier($identifier);
        if ($foundBudgetItem)
        {
            return $foundBudgetItem;
        }

        $budgetItem = new BudgetItem($identifier, $name);
        $this->entityManager->persist($budgetItem);
        return $budgetItem;
    }

    /**
     * 
     * @param type $budgetItem
     * @param type $invoice
     * @param type $budgetItemAmount
     */
    private function createInvoiceItem($budgetItem, $invoice, $budgetItemAmount)
    {
        $foundInvoiceItem = $this->invoiceRepository->findItemByInvoiceAndBudgetItem($budgetItem, $invoice);
        if (!$foundInvoiceItem)
        {
            $invoiceItem = new InvoiceItem($invoice, $budgetItem, $budgetItemAmount);
            $this->entityManager->persist($invoiceItem);
        }
    }

    /**
     * 
     * @param type $identifier
     * @param type $type
     * @param type $distinction
     * @param type $vatRecord
     * @param type $amount
     * @param type $amountWithoutVat
     * @param type $amountOriginal
     * @param type $amountPaid
     * @param type $amountPaidOriginal
     * @param type $currency
     * @param \DateTime $issued
     * @param \DateTime $received
     * @param \DateTime $paid
     * @param \DateTime $maturity
     * @param type $description
     * @param type $supplierIdentifier
     * @param type $supplierName
     * @param type $supplierCompanyIdentifier
     * @param int $budgetItemIdentifier
     * @param string $budgetItemName
     * @param type $budgetItemAmount
     */
    public function setInvoice($identifier, $type, $distinction, $vatRecord, $amount, $amountWithoutVat, $amountOriginal, $amountPaid, $amountPaidOriginal, $currency, \DateTime $issued, \DateTime $received, \DateTime $paid, \DateTime $maturity, $description, $supplierIdentifier, $supplierName, $supplierCompanyIdentifier, $budgetItemIdentifier, $budgetItemName, $budgetItemAmount
    )
    {

        $foundInvoice = $this->invoiceRepository->findByIdentifier($identifier);
        if ($foundInvoice)
        {
            $invoice = $foundInvoice;
        }
        else
        {
            if ($supplierIdentifier)
            {
                $supplier = $this->createSupplier($supplierIdentifier, $supplierCompanyIdentifier, $supplierName);
            }
            else
            {
                $supplier = null;
            }
            $invoice = new Invoice($identifier, $type, $distinction, $vatRecord, $amount, $amountWithoutVat, $amountOriginal, $amountPaid, $amountPaidOriginal, $currency, $issued, $received, $maturity, $paid, $description, $supplier);
        }

        $this->entityManager->persist($invoice);

        if (!$budgetItemName)
        {
            $budgetItemName = 'Rozpočtová položka neurčena';

            if (!$budgetItemIdentifier)
            {
                $budgetItemIdentifier = 0;
            }
        }

        if ($budgetItemAmount)
        {
            $budgetItem = $this->createBudgetItem($budgetItemIdentifier, $budgetItemName);
            $this->createInvoiceItem($budgetItem, $invoice, $budgetItemAmount);
        }

        $this->entityManager->flush();
    }

}
