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
use Nette\Caching\Cache;

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
    
    /**
     *
     * @var type 
     */
    private $cache;
    
    public function startup()
    {
        parent::startup();
        $this->cache = new Cache($this->cacheStorage, 'Ajax');
    }

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
    
    public function renderBudgetGroups($budgetGroupId = null)
    {
        $data = $this->ajaxCache(__FUNCTION__.$budgetGroupId, function() use($budgetGroupId){
            $finalArray = [];
            $filter = [];
            $min = 0;
            $max = 0;
            $total = 0;
            if (!is_null($budgetGroupId))
            {
                $filter['id'] = $budgetGroupId;
            }
            $s = $this->budgetGroupRepository->getBudgetGroupRepository()->findBy($filter);
            foreach($s AS $u)
            {   
                $group = [];
                $group['id'] = $u->getSlug();
                $group['nazev'] = $u->getName();
                $group['popis'] = $u->getDescription();
                $group['x'] = $u->getX();
                $group['y'] = $u->getY();
                $group['barva'] = $u->getColor();
                $group['max_uhrazeno_udt'] = 10000; //!FIXME
                $group['min_uhrazeno_udt'] = 20; //!FIXME
                $group['objem'] = 0;
                $group['pocet'] = 0;
                $group['polozky'] = [];
                foreach($u->getBudgetItems() AS $i)
                {
                    $item = [];
                    $item['id'] = $i->getId();
                    $item['nazev'] = $i->getName();
                    $amount = 0;
                    foreach($i->getinvoiceItems() AS $invoiceItem)
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
                        $objem[$key]  = $row['objem'];
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
                    $objem[$key]  = $row['objem'];
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
        
        
        $this->payload->result = $data;
        $this->payload->success = true;//!FIXME
        $this->payload->error = null; //!FIXME
        
        $this->sendPayload();
    }
    
    public function renderSupplier($supplierIdentifier)
    {
        $data = $this->ajaxCache(__FUNCTION__.$supplierIdentifier, function() use($supplierIdentifier){
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
                $data = @file_get_contents("http://kamos.datlab.cz/company/CZ".$supplier->getCompanyIdentifier());

                if ($data)
                {
                    $kamos = json_decode($data);

                    $entitiesDecode = ["company_name"];
                    foreach($entitiesDecode as $key)
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
        
        $this->payload->result = $data;
        $this->payload->success = true;//!FIXME
        $this->payload->error = null; //!FIXME
        
        $this->sendPayload();
    }
}
