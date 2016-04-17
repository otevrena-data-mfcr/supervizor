<?php

namespace Supervizor\Budget;

use Doctrine\Common\Collections\ArrayCollection;
use Nette\Caching\Cache;
use Supervizor\Invoice\InvoiceItem;
use Supervizor\Storage\CacheStorage;

class BudgetFacade
{
    /** @var BudgetRepository */
    private $budgetGroupRepository;

    /** @var CacheStorage */
    private $cacheStorage;



    /**
     * BudgetFacade constructor.
     * @param CacheStorage $cacheStorage
     * @param BudgetRepository $budgetGroupRepository
     */
    public function __construct(CacheStorage $cacheStorage, BudgetRepository $budgetGroupRepository)
    {
        $this->cacheStorage = $cacheStorage;
        $this->budgetGroupRepository = $budgetGroupRepository;
    }



    public function getBudgetGroups($budgetGroupId = null)
    {
        $key = 'BudgetGroup' . $budgetGroupId;
        $fallback = function (& $dependencies) use ($budgetGroupId) {
            $dependencies[Cache::EXPIRE] = CacheStorage::EXPIRATION;

            $finalArray = [];
            $filter = [];
            $min = 0;
            $max = 0;
            $total = 0;
            if (!is_null($budgetGroupId)) {
                $filter['id'] = $budgetGroupId;
            }

            /** @var ArrayCollection $s */
            $s = $this->budgetGroupRepository->getBudgetGroupRepository()->findBy($filter);

            /** @var BudgetGroup $u */
            foreach ($s AS $u) {
                $group = [];
                $group['id'] = $u->getSlug();
                $group['nazev'] = $u->getName();
                $group['popis'] = $u->getDescription();
                $group['x'] = $u->getX();
                $group['y'] = $u->getY();
                $group['barva'] = $u->getColor();
                $group['max_uhrazeno_udt'] = 0;
                $group['min_uhrazeno_udt'] = 0;
                $group['objem'] = 0;
                $group['pocet'] = 0;
                $group['polozky'] = [];
                foreach ($u->getBudgetItems() AS $i) {
                    $item = [];
                    $item['id'] = $i->getIdentifier();
                    $item['nazev'] = $i->getName();
                    $amount = 0;
                    /** @var InvoiceItem $invoiceItem */
                    foreach ($i->getinvoiceItems() AS $invoiceItem) {
                        $amount += $invoiceItem->getAmount();
                        $paidTimestamp = $invoiceItem->getInvoice()->getPaid()->getTimestamp();

                        $group['max_uhrazeno_udt'] = max($group['max_uhrazeno_udt'], $paidTimestamp);
                        if (!$group['min_uhrazeno_udt'])
                        {
                            $group['min_uhrazeno_udt'] = $paidTimestamp;
                        }
                        else
                        {
                            $group['min_uhrazeno_udt'] = min($group['min_uhrazeno_udt'], $paidTimestamp);
                        }
                    }
                    $group['objem'] += $item['objem'] = $amount;
                    $group['pocet'] += $item['pocet'] = $i->getinvoiceItems()->count();
                    $item['skupina_id'] = $u->getId();
                    $group['polozky'][] = $item;

                    $objem = [];
                    foreach ($group['polozky'] as $key => $row) {
                        $objem[$key] = $row['objem'];
                    }
                    array_multisort($objem, SORT_DESC, $group['polozky']);
                }

                $max = max($group['objem'], $max);

                if (!$min) {
                    $min = $group['objem'];
                } else {
                    $min = min($group['objem'], $min);
                }

                $total += $group['objem'];
                $finalArray[$u->getSlug()] = $group;

                $objem = [];
                foreach ($finalArray as $key => $row) {
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
        };

        return $this->cacheStorage->load($key, $fallback);
    }

}
