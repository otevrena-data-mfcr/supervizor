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
    
    //create dodavatel
		/*$this->affected += $pdo->exec("INSERT INTO dodavatel (id,ico_st,nazev_st) (SELECT dodavatel_id,MAX(COALESCE(dodavatel_ico_st,'00000000')),MAX(dodavatel_nazev_st) FROM raw_load WHERE dodavatel_id IS NOT NULL GROUP BY dodavatel_id)");
		//create polozka
		$this->affected += $pdo->exec("INSERT INTO polozka (id,nazev_st) (SELECT COALESCE(polozka_id,0),MAX(COALESCE(polozka_nazev_st,'')) FROM raw_load GROUP BY polozka_id)");
		//create faktura
		$this->affected += $pdo->exec("INSERT INTO faktura (id,dodavatel_id,typ_dokladu_st,rozliseni_st,evidence_dph_in,castka_am,castka_bez_dph_am,castka_orig_am,uhrazeno_am,uhrazeno_orig_am,mena_curr,vystaveno_dt,prijato_dt,splatnost_dt,uhrazeno_dt,ucel_tx) (SELECT faktura_id, MAX(dodavatel_id), MAX(typ_dokladu_st), MAX(rozliseni_st), MAX(evidence_dph_in), MAX(castka_am), MAX(castka_bez_dph_am), MAX(castka_orig_am), SUM(polozka_castka_am), MAX(uhrazeno_orig_am), MAX(mena_curr), MAX(vystaveno_dt), MAX(prijato_dt), MAX(splatnost_dt), MAX(uhrazeno_dt), MAX(ucel_tx) FROM raw_load WHERE faktura_id IS NOT NULL GROUP BY faktura_id)");
		//create faktura_polozka
		$this->affected += $pdo->exec("INSERT INTO faktura_polozka (faktura_id,polozka_id,castka_am) (SELECT faktura_id,COALESCE(polozka_id,0),MAX(CASE WHEN polozka_id IS NOT NULL THEN polozka_castka_am ELSE castka_am END) FROM raw_load GROUP BY faktura_id,polozka_id HAVING COUNT(1) = 1)");
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
    
    private function createInvoiceItem($budgetItem, $invoice, $budgetItemAmount)
    {
        $foundInvoiceItem = $this->invoiceRepository->findItemByInvoiceAndBudgetItem($budgetItem, $invoice);
        if (!$foundInvoiceItem)
        {
            $invoiceItem = new InvoiceItem($invoice, $budgetItem, $budgetItemAmount);
            $this->entityManager->persist($invoiceItem);
        }
    }
    
    public function setInvoice($identifier, 
            $type, 
            $distinction, 
            $vatRecord, 
            $amount, 
            $amountWithoutVat, 
            $amountOriginal, 
            $amountPaid, 
            $amountPaidOriginal, 
            $currency, 
            \DateTime $issued, 
            \DateTime $received,
            \DateTime $paid,
            \DateTime $maturity,
            $description,
            
            $supplierIdentifier, 
            $supplierName,
            $supplierCompanyIdentifier,
            
            $budgetItemIdentifier,
            $budgetItemName,
            $budgetItemAmount
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
