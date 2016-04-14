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

namespace Supervizor\Application;

use Nette\Http\IResponse;
use Supervizor\Budget\BudgetGroup;
use Supervizor\Budget\BudgetRepository;
use Supervizor\Invoice\InvoiceItem;
use Supervizor\Invoice\InvoiceRepository;

/**
 * Class InvoicePresenter
 */
class InvoicePresenter extends Presenter
{
    /** @var InvoiceRepository @inject */
    public $invoiceRepository;

    /** @var BudgetRepository @inject */
    public $budgetRepository;


    /**
     * @param bool $popup
     */
    public function renderDefault($invoiceIdentifier, $popup = false)
    {
        $invoice = $this->invoiceRepository->findByIdentifier($invoiceIdentifier);
        if (!$invoice) {
            $this->error('Invoice not found!', IResponse::S404_NOT_FOUND);
        }
        $amount = 0;
        /** @var BudgetGroup $budgetGroup */
        $budgetGroup = $this->budgetRepository->getGroupByInvoice($invoice);

        foreach ($budgetGroup->getBudgetItems() AS $budgetItem) {
            /** @var InvoiceItem $invoiceItem */
            foreach ($budgetItem->getinvoiceItems() AS $invoiceItem) {
                $amount += $invoiceItem->getAmount();
            }
        }

        $this->template->budgetGroup = $budgetGroup;
        $this->template->budgetGroupSum = $amount;
        $this->template->invoice = $invoice;


        if ($popup) {
            $this->setLayout('popuplayout');
        }
        $this->template->popup = $popup;

        $this->template->title = 'O projektu';
    }

}
