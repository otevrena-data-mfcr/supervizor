<?php

namespace Supervizor\Supplies;

use Supervizor\Budget\BudgetRepository;
use Supervizor\Invoice\InvoiceRepository;
use Supervizor\NotFoundException;
use Supervizor\Storage\CacheStorage;

class SupplierFacade
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

    /** @var CacheStorage */
    private $cacheStorage;

    /** @var SupplierRepository */
    private $supplierRepository;

    /** @var BudgetRepository */
    private $budgetGroupRepository;

    /** @var InvoiceRepository */
    private $invoiceRepository;



    /**
     * SupplierFacade constructor.
     * @param CacheStorage $cacheStorage
     * @param SupplierRepository $supplierRepository
     * @param BudgetRepository $budgetGroupRepository
     * @param InvoiceRepository $invoiceRepository
     */
    public function __construct(
        CacheStorage $cacheStorage,
        SupplierRepository $supplierRepository,
        BudgetRepository $budgetGroupRepository,
        InvoiceRepository $invoiceRepository
    ) {
        $this->cacheStorage = $cacheStorage;
        $this->supplierRepository = $supplierRepository;
        $this->budgetGroupRepository = $budgetGroupRepository;
        $this->invoiceRepository = $invoiceRepository;
    }



    /**
     * @param $supplierIdentifier
     * @return mixed|NULL
     * @throws NotFoundException
     */
    public function getSupplier($supplierIdentifier)
    {
        $key = 'Supplier' . $supplierIdentifier;
        $fallback = function () use ($supplierIdentifier) {
            $supplier = $this->supplierRepository->findByIdentifier($supplierIdentifier);

            if (!$supplier) {
                throw new NotFoundException();
            }

            $result = [
                'id' => $supplier->getIdentifier(),
                'db' => [
                    'id' => $supplier->getIdentifier(),
                    'ico_st' => $supplier->getCompanyIdentifier(),
                    'nazev_st' => $supplier->getName()
                ]
            ];

            if ($supplier->getCompanyIdentifier()) {
                $data = @file_get_contents("http://kamos.datlab.cz/company/CZ" . $supplier->getCompanyIdentifier());

                if ($data) {
                    $kamos = json_decode($data);

                    $entitiesDecode = ["company_name"];
                    foreach ($entitiesDecode as $key) {
                        if (property_exists($kamos, $key)) {
                            $kamos->{$key} = htmlspecialchars_decode($kamos->{$key});
                        }
                    }
                } else {
                    $kamos = null;
                }

                $result['kamos'] = $kamos;
            }

            return $result;
        };

        return $this->cacheStorage->load($key, $fallback);
    }



    /**
     * @param null $budgetGroupSlug
     * @param int $page
     * @param array $budgetItems
     * @param null $dateFrom
     * @param null $dateTo
     * @return array
     * @throws NotFoundException
     */
    public function getSuppliers($budgetGroupSlug = null, $page = 1, array $budgetItems = [], $dateFrom = null, $dateTo = null)
    {
        $limit = 10;
        $suppliersOut = $this->getSupplierOut($budgetGroupSlug, $budgetItems, $dateFrom, $dateTo);

        $suppliersTotal = count($suppliersOut);
        $realPage = $page - 1;
        $chunked = array_chunk($suppliersOut, $limit);
        if (array_key_exists($realPage, $chunked)) {
            $pageItems = $chunked[$realPage];
        } else {
            $pageItems = [];
        }

        return [
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
    }



    private function getSupplierOut($budgetGroupSlug = null, array $budgetItems = [], $dateFrom = null, $dateTo = null)
    {
        $key = 'Suppliers' . md5(json_encode([$budgetGroupSlug, $budgetItems, $dateFrom, $dateTo]));

        $fallback = function () use ($budgetGroupSlug, $budgetItems, $dateFrom, $dateTo) {
            $budgetGroup = $this->budgetGroupRepository->findGroupBySlug($budgetGroupSlug);

            if (!$budgetGroup) {
                throw new NotFoundException();
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

                foreach ($this->invoiceRepository->getBySupplierAndGroup($supplier, $budgetGroup, $budgetItems, $dateFrom, $dateTo) AS $invoiceSrc) {
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

                    $invoiceItems = [];
                    foreach ($invoiceSrc->getInvoiceItems() AS $invoiceItemSrc) {
                        $invoiceItem = [];
                        $invoiceItem['faktura_id'] = $invoiceSrc->getIdentifier();
                        $invoiceItem['polozka_id'] = $invoiceItemSrc->getBudgetItem()->getIdentifier();
                        $invoice['detail_castka_am'] += $invoiceItem['castka_am'] = $invoiceItemSrc->getAmount();
                        $invoiceItem['nazev_st'] = $invoiceItemSrc->getBudgetItem()->getName();
                        $invoiceItem['ve_vyberu'] = in_array($invoiceSrc->getIdentifier(), $budgetItems);
                        $invoiceItems[] = $invoiceItem;
                    }
                    $invoice['polozky'] = $invoiceItems;

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
        };

        return $this->cacheStorage->load($key, $fallback);
    }
}
