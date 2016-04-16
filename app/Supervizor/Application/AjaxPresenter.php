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

use Nette\Http\Context;
use Supervizor\Budget\BudgetFacade;
use Supervizor\Invoice\InvoiceRepository;
use Supervizor\NotFoundException;
use Supervizor\Supplies\SupplierFacade;

class AjaxPresenter extends Presenter
{

    /** @var InvoiceRepository @inject */
    public $invoiceRepository;

    /** @var Context @inject */
    public $httpContext;

    /** @var BudgetFacade @inject */
    public $budgetFacade;

    /** @var SupplierFacade @inject */
    public $supplierFacade;



    /**
     * @param null $budgetGroupId
     */
    public function renderBudgetGroups($budgetGroupId = null)
    {
        $this->checkForChanges();

        $data = $this->budgetFacade->getBudgetGroups($budgetGroupId);

        $this->process($data);
    }



    /**
     * @param $supplierIdentifier
     */
    public function renderSupplier($supplierIdentifier)
    {
        $this->checkForChanges();

        try {
            $data = $this->supplierFacade->getSupplier($supplierIdentifier);
        } catch (NotFoundException $e) {
            $this->error(sprintf("Supplier '%s' not found", $supplierIdentifier));
        }

        $this->process($data);
    }



    /**
     * @param null $budgetGroupSlug
     * @param int $page
     * @param array $budgetItems
     * @param null $dateFrom
     * @param null $dateTo
     */
    public function renderSuppliers($budgetGroupSlug = null, $page = 1, array $budgetItems = [], $dateFrom = null, $dateTo = null)
    {
        $this->checkForChanges();

        try {
            $data = $this->supplierFacade->getSuppliers($budgetGroupSlug, $page, $budgetItems, $dateFrom, $dateTo);
        } catch (NotFoundException $e) {
            $this->error('Subject not found');
        }

        $this->process($data);
    }



    private function checkForChanges()
    {
        $lastUpdatedInvoice = $this->invoiceRepository->getLastUpdated();
        if (!$this->httpContext->isModified($lastUpdatedInvoice->getUpdated())) {
            $this->terminate();
        }
    }



    /**
     * @param $data
     */
    private function process($data)
    {
        if ($this->isAjax()) {
            $this->payload->result = $data;

            //!Not used, exceptions MUST kill app to get logged
            $this->payload->success = true;
            $this->payload->error = null;

            $this->sendPayload();
        } else {
            $this->dataOut($data);
        }
    }



    /**
     * @param $data
     * @throws \Nette\Application\AbortException
     */
    private function dataOut($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        $this->terminate();
    }

}
