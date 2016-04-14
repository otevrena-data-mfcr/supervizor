<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Supervizor\Utils\Importer;

/**
 * Description of IImportTarget
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
interface IImportTarget
{

    /**
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
     * @param \DateTime $issued
     * @param \DateTime $received
     * @param \DateTime $paid
     * @param \DateTime $maturity
     * @param $description
     * @param $supplierIdentifier
     * @param $supplierName
     * @param $supplierCompanyIdentifier
     * @param $budgetItemIdentifier
     * @param $budgetItemName
     * @param $budgetItemAmount
     * @return mixed
     */
    public function setInvoice(
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
        $budgetItemAmount,
        $import
    );

    /**
     * @param $name
     * @param $slug
     * @param $isDefault
     * @return mixed
     */
    public function setImportGroup($name, $slug, $isDefault);

    /**
     * @param $importGroupId
     * @param $name
     * @param $slug
     * @param $description
     * @param $homepage
     * @param $isDefault
     * @return mixed
     */
    public function setImport($importGroup, $name, $slug, $description, $homepage, $isDefault);
}
