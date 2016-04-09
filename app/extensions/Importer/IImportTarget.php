<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Extensions\Importer;

/**
 * Description of IImportTarget
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
interface IImportTarget
{

    public function setInvoice($identifier, $type, $distinction, $vatRecord, $amount, $amountWithoutVat, $amountOriginal, $amountPaid, $amountPaidOriginal, $currency, \DateTime $issued, \DateTime $received, \DateTime $paid, \DateTime $maturity, $description, $supplierIdentifier, $supplierName, $supplierCompanyIdentifier, $budgetItemIdentifier, $budgetItemName, $budgetItemAmount
    );
}
