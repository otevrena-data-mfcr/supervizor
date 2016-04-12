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

use App\Model\Repository\InvoiceRepository;
use App\Model\Repository\BudgetRepository;
use App\Model\Repository\SupplierRepository;
use App\Model\Entities\BudgetGroup;
use Doctrine\Common\Collections\ArrayCollection;
use App\Model\Entities\InvoiceItem;
use App\Model\Entities\Supplier;
use App\Model\Entities\Invoice;
use Nette\Caching\Cache;
use Nette\Http\IResponse;

class AjaxPresenter extends BasePresenter
{

    /** @var InvoiceRepository @inject */
    public $invoiceRepository;

    /** @var BudgetRepository @inject */
    public $budgetGroupRepository;

    /** @var SupplierRepository @inject */
    public $supplierRepository;
    
    /** @var Nette\Http\Context @inject */
    public $httpContext;

    /** @var Nette\Caching\IStorage @inject */
    public $cacheStorage;

    const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var Cache
     */
    private $cache;


    public function startup()
    {
        parent::startup();
        $this->cache = new Cache($this->cacheStorage, 'Ajax');
    }

    /**
     * @param $name
     * @param callable $dataSource
     * @return mixed|NULL
     * @throws Exception
     * @throws Throwable
     * @throws \Nette\Application\AbortException
     */
    private function ajaxCache($name, callable $dataSource)
    {
        $lastUpdatedInvoice = $this->invoiceRepository->getLastUpdated();
        if (!$this->httpContext->isModified($lastUpdatedInvoice->getUpdated()))
        {
            $this->terminate();
        }

        $data = $this->cache->load($name);
        if (!$data)
        {
            $data = $dataSource();
            $this->cache->save($name, $data, array(
                Cache::EXPIRE => '5 hours'
            ));
        }
        return $data;
    }

    /**
     * @param null $budgetGroupId
     */
    public function renderBudgetGroups($budgetGroupId = null)
    {
        $data = $this->ajaxCache(__FUNCTION__ . $budgetGroupId, function() use($budgetGroupId)
        {
            $finalArray = [];
            $filter = [];
            $min = 0;
            $max = 0;
            $total = 0;
            if (!is_null($budgetGroupId))
            {
                $filter['id'] = $budgetGroupId;
            }

            /** @var ArrayCollection $s */
            $s = $this->budgetGroupRepository->getBudgetGroupRepository()->findBy($filter);

            /** @var BudgetGroup $u */
            foreach ($s AS $u)
            {
                $group = [];
                $group['id'] = $u->getSlug();
                $group['nazev'] = $u->getName();
                $group['popis'] = $u->getDescription();
                $group['x'] = $u->getX();
                $group['y'] = $u->getY();
                $group['barva'] = $u->getColor();
                $group['max_uhrazeno_udt'] = (new \DateTime)->modify('+2 months')->getTimestamp(); //!FIXME
                $group['min_uhrazeno_udt'] = (new \DateTime)->modify('-1 year')->getTimestamp(); //!FIXME
                $group['objem'] = 0;
                $group['pocet'] = 0;
                $group['polozky'] = [];
                foreach ($u->getBudgetItems() AS $i)
                {
                    $item = [];
                    $item['id'] = $i->getIdentifier();
                    $item['nazev'] = $i->getName();
                    $amount = 0;
                    /** @var InvoiceItem $invoiceItem */
                    foreach ($i->getinvoiceItems() AS $invoiceItem)
                    {
                        $amount += $invoiceItem->getAmount();
                    }
                    $group['objem'] += $item['objem'] = $amount;
                    $group['pocet'] += $item['pocet'] = $i->getinvoiceItems()->count();
                    $item['skupina_id'] = $u->getId();
                    $group['polozky'][] = $item;

                    $objem = [];
                    foreach ($group['polozky'] as $key => $row)
                    {
                        $objem[$key] = $row['objem'];
                    }
                    array_multisort($objem, SORT_DESC, $group['polozky']);
                }

                $max = max($group['objem'], $max);

                if (!$min)
                {
                    $min = $group['objem'];
                }
                else
                {
                    $min = min($group['objem'], $min);
                }

                $total += $group['objem'];
                $finalArray[$u->getSlug()] = $group;

                $objem = [];
                foreach ($finalArray as $key => $row)
                {
                    $objem[$key] = $row['objem'];
                }
                array_multisort($objem, SORT_DESC, $finalArray);
            }

            return [
                'skupiny' => $finalArray,
                'stats' => [
                    'min' => $min,
                    'max' => $max,
                    'total' => $total
                ]
            ];
        });

        if ($this->isAjax())
        {
            $this->payload->result = $data;

            //!Not used, exceptions MUST kill app to get logged
            $this->payload->success = true;
            $this->payload->error = null;

            $this->sendPayload();
        }
        else
        {
            $this->dataOut($data);
        }
    }

    /**
     * @param $supplierIdentifier
     */
    public function renderSupplier($supplierIdentifier)
    {
        $data = $this->ajaxCache(__FUNCTION__ . $supplierIdentifier, function() use($supplierIdentifier)
        {
            $supplier = $this->supplierRepository->findByIdentifier($supplierIdentifier);

            if (!$supplier)
            {
                throw new \Nette\Application\BadRequestException;
            }

            $result = [
                'id' => $supplier->getIdentifier(),
                'db' => [
                    'id' => $supplier->getIdentifier(),
                    'ico_st' => $supplier->getCompanyIdentifier(),
                    'nazev_st' => $supplier->getName()
                ]
            ];

            if ($supplier->getCompanyIdentifier())
            {
                $data = @file_get_contents("http://kamos.datlab.cz/company/CZ" . $supplier->getCompanyIdentifier());

                if ($data)
                {
                    $kamos = json_decode($data);

                    $entitiesDecode = ["company_name"];
                    foreach ($entitiesDecode as $key)
                    {
                        if (property_exists($kamos, $key))
                        {
                            $kamos->{$key} = htmlspecialchars_decode($kamos->{$key});
                        }
                    }
                }
                else
                {
                    $kamos = null;
                }

                $result['kamos'] = $kamos;
            }

            return $result;
        });

        if ($this->isAjax())
        {
            $this->payload->result = $data;

            //!Not used, exceptions MUST kill app to get logged
            $this->payload->success = true;
            $this->payload->error = null;

            $this->sendPayload();
        }
        else
        {
            $this->dataOut($data);
        }
    }

    /**
     * @param null $budgetGroupSlug
     * @param int $page
     * @param array $budgetItems
     * @param null $dateFrom
     * @param null $dateTo
     * @throws \Nette\Application\BadRequestException
     */
    public function renderSuppliers($budgetGroupSlug = null, $page = 1, array $budgetItems = [], $dateFrom = null, $dateTo = null)
    {
        $limit = 10;
        $suppliersOut = $this->ajaxCache(__FUNCTION__ . md5(json_encode([
                $budgetGroupSlug,
                $budgetItems,
                $dateFrom,
                $dateTo
            ])), function () use ($budgetGroupSlug, $budgetItems, $dateFrom, $dateTo) {
            $budgetGroup = $this->budgetGroupRepository->findGroupBySlug($budgetGroupSlug);

            if (!$budgetGroup) {
                $this->error('Subject not found!', IResponse::S404_NOT_FOUND);
            }


            $qb = $this->supplierRepository->getSupplierRepository()->createQueryBuilder('s');
            $qb->select('s')
                ->join('s.invoices', 'i')
                ->join('i.invoiceItems', 'ii')
                ->join('ii.budgetItem', 'bi')
                ->join('bi.budgetGroup', 'bg')
                ->groupBy('s.identifier');

            if ($budgetGroupSlug) {
                $qb->andWhere('bg.slug = :slug')
                    ->setParameter('slug', $budgetGroupSlug);
            }


            if (!empty($budgetItems)) {
                $qb->andWhere('bi.identifier IN (:budget_items)')
                    ->setParameter('budget_items', $budgetItems);
            }

            if ($dateFrom) {
                $qb->andWhere('i.issued >= :issued_from')
                    ->setParameter('issued_from', new \DateTime('@' . (int)$dateFrom));
            }

            if ($dateTo) {
                $qb->andWhere('i.issued <= :issued_to')
                    ->setParameter('issued_to', new \DateTime('@' . (int)$dateTo));
            }

            $all = $qb->getQuery()->getResult();

            $suppliersOut = [];

            /** @var Supplier $supplier */
            foreach ($all AS $supplier) {
                $supplierOut = [];
                $supplierOut['id'] = $supplier->getIdentifier();
                $supplierOut['ico_st'] = $supplier->getCompanyIdentifier();
                $supplierOut['nazev_st'] = $supplier->getName();
                $supplierOut['castka_celkem_am'] = 0;
                $supplierOut['pocet_celkem_no'] = 0;

                $invoices = [];

                /** @var Invoice $invoiceSrc */
                foreach ($this->invoiceRepository->getBySupplierAndGroup($supplier, $budgetGroup, $budgetItems,
                    $dateFrom, $dateTo) AS $invoiceSrc) {
                    $supplierOut['pocet_celkem_no']++;

                    $invoice = [];
                    $invoice['id'] = $invoiceSrc->getIdentifier();
                    $invoice['dodavatel_id'] = $supplier->getIdentifier();
                    $invoice['typ_dokladu_st'] = $invoiceSrc->getType();
                    $invoice['rozliseni_st'] = $invoiceSrc->getDistinction();
                    $invoice['evidence_dph_in'] = $invoiceSrc->getVatRecord();
                    $invoice['castka_am'] = $invoiceSrc->getAmount();
                    $invoice['castka_bez_dph_am'] = $invoiceSrc->getAmountWithoutVat();
                    $invoice['castka_orig_am'] = $invoiceSrc->getAmountOriginal();
                    $invoice['uhrazeno_am'] = $invoiceSrc->getAmountPaid();
                    $invoice['uhrazeno_orig_am'] = $invoiceSrc->getAmountPaidOriginal();
                    $invoice['mena_curr'] = $invoiceSrc->getCurrency();
                    $invoice['vystaveno_dt'] = $invoiceSrc->getIssued()->format(self::DATE_FORMAT);
                    $invoice['prijato_dt'] = $invoiceSrc->getReceived()->format(self::DATE_FORMAT);
                    $invoice['splatnost_dt'] = $invoiceSrc->getMaturity()->format(self::DATE_FORMAT);
                    $invoice['uhrazeno_dt'] = $invoiceSrc->getPaid()->format(self::DATE_FORMAT);
                    $invoice['ucel_tx'] = $invoiceSrc->getDescription();
                    $invoice['uhrazeno_udt'] = $invoiceSrc->getPaid()->getTimestamp();
                    $invoice['detail_castka_am'] = 0;

                    $inoviceItems = [];
                    foreach ($invoiceSrc->getinvoiceItems() AS $invoiceItemSrc) {
                        $invoiceItem = [];
                        $invoiceItem['faktura_id'] = $invoiceSrc->getIdentifier();
                        $invoiceItem['polozka_id'] = $invoiceItemSrc->getBudgetItem()->getIdentifier();
                        $invoice['detail_castka_am'] += $invoiceItem['castka_am'] = $invoiceItemSrc->getAmount();
                        $invoiceItem['nazev_st'] = $invoiceItemSrc->getBudgetItem()->getName();
                        $invoiceItem['ve_vyberu'] = in_array($invoiceSrc->getIdentifier(), $budgetItems);
                        $inoviceItems[] = $invoiceItem;
                    }
                    $invoice['polozky'] = $inoviceItems;

                    $supplierOut['castka_celkem_am'] += $invoice['detail_castka_am'];
                    $invoices[] = $invoice;
                }

                $supplierOut['faktury'] = $invoices;

                $suppliersOut[] = $supplierOut;
            }

            $amountTotal = [];
            foreach ($suppliersOut as $key => $row) {
                $amountTotal[$key] = $row['castka_celkem_am'];
            }
            array_multisort($amountTotal, SORT_DESC, $suppliersOut);

            return $suppliersOut;
        });

        $suppliersTotal = count($suppliersOut);
        $realPage = $page - 1;
        $chunked = array_chunk($suppliersOut, $limit);
        if (array_key_exists($realPage, $chunked))
        {
            $pageItems = $chunked[$realPage];
        }
        else
        {
            $pageItems = [];
        }


        $result = [
            'dodavatele' => $pageItems,
            'pager' => [
                'pages' => ceil($suppliersTotal / $limit),
                'total' => $suppliersTotal,
                'current' => $page,
                'previous' => $page > 1 ? $page - 1 : null,
                'next' => $page + 1,
                'offset' => 0,
                'limit' => $limit,
                'start' => 1,
                'end' => $limit
            ]
        ];


        if ($this->isAjax())
        {
            $this->payload->result = $result;

            //!Not used, exceptions MUST kill app to get logged
            $this->payload->success = true;
            $this->payload->error = null;
            $this->sendPayload();
        }
        else
        {
            $this->dataOut($result);
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
