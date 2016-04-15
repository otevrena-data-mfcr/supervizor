<?php

namespace Supervizor\DI\Importer\Parsers;

use Nette;

class Mmr extends ImportParser implements IImportParser
{

    /**
     * @var
     */
    private $info;

    /**
     *
     * @var Nette\Caching\Cache
     */
    private $cache;

    /**
     * @var
     */
    private $target;

    /**
     * @var
     */
    private $import;

    /**
     * Mmr constructor.
     * @param Nette\Caching\Cache $cache
     * @param $source
     * @param $target
     */
    public function __construct(Nette\Caching\Cache $cache, $source, $target, $import)
    {
        $this->cache = $cache;
        $this->target = $target;
        $this->import = $import;

        $data = @file_get_contents($source);
        if (!$data)
        {
            throw new ImporterBadSourceException($source);
        }

        $json = json_decode($data);
        if (!$json->success)
        {
            throw new ImporterBadSourceException($source);
        }

        $this->info = $json->result;

        $this->proccess();
    }

    /**
     * converts shitty date to DateTime
     * @param string $mess
     * @return \DateTime|boolean
     */
    private function mess2DateTime($mess)
    {
        $months = [
            "Leden" => 1,
            "Únor" => 2,
            "Březen" => 3,
            "Duben" => 4,
            "Květen" => 5,
            "Červen" => 6,
            "Červenec" => 7,
            "Srpen" => 8,
            "Září" => 9,
            "Říjen" => 10,
            "Listopad" => 11,
            "Prosinec" => 12
        ];

        $parts = [];
        $match = preg_match("/^[^\t]*\t(\d{1,2})\. (\w+) (\d{4}) \- (\d{1,2})\:(\d{1,2})$/", $mess, $parts);

        if (!$match)
            return false;

        return new \DateTime('@' . mktime($parts[4], $parts[5], 0, $months[$parts[2]], $parts[1], $parts[3]));
    }

    /**
     * 
     * @param string $cs
     * @return float
     */
    private function cs2float($cs)
    {
        return (float) strtr($cs, array(',' => '.', ' ' => ''));
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function proccess()
    {
        $parsedDataKey = $this->info->id . '_parsedData';
        $lastModifiedKey = $this->info->id . '_lastModified';

        $lastModified = $this->cache->load($lastModifiedKey);
        if (!$lastModified || $this->mess2DateTime($lastModified) < $this->mess2DateTime($this->info->last_modified))
        {
            $this->cache->save($lastModifiedKey, $this->info->last_modified);
            $parsedData = $this->parse();
            $this->cache->save($parsedDataKey, $parsedData);
        }
        else
        {
            $parsedData = $this->cache->load($parsedDataKey, function(& $depedencies) use($parsedDataKey)
            {
                $parsedData = $this->parse();
                $this->cache->save($parsedDataKey, $parsedData);
                return $parsedData;
            });
        }

        foreach ($parsedData AS $inovice)
        {
            call_user_func_array(array($this->target, 'setInvoice'), $inovice);
        }
    }

    /**
     * @return mixed
     */
    public function parse()
    {
        $csvArray = file($this->info->url);
        //Remove first row of CSV
        unset($csvArray[0]);
        $csv = array_map(function($rowRaw)
        {
            //Convert from fag shit to UTF-8
            $rowRaw = iconv("WINDOWS-1250", "UTF-8", $rowRaw);
            $row = str_getcsv($rowRaw, ';');

            return [
                'identifier' => join("-", array("MMR", $row[0], $row[1], $row[2])),
                'type' => "Faktura", //!FIXME TO CONSTANTS
                'distinction' => null,
                'vat_record' => null,
                'ammount' => null,
                'amountWithoutVat' => null,
                'amountOriginal' => null,
                'amountPaid' => null,
                'amountPaidOriginal' => null,
                'currency' => $row[7] ? strtr($row[7], ["KČ" => "CZK"]) : null,
                'issued' => null,
                'received' => $row[8] ? new \DateTime($row[8]) : null,
                'maturity' => null,
                'paid' => $row[9] ? new \DateTime($row[9]) : null,
                'description' => $row[10] ? : null,
                'supplierIdentifier' => $row[5] ? : substr(md5($row[4]), 0, 8),
                'supplierName' => $row[4] ? : null,
                'supplierCompanyId' => $row[5] ? str_pad($row[5], 8, "0", STR_PAD_LEFT) : null,
                'budgetItemIdentifier' => ((int) $row[11]) ? : null,
                'budgetItemName' => $row[12] ? : null,
                'budgetItemAmount' => $row[6] ? $this->cs2float($row[6]) : null,
                'importId' => $this->import
            ];
        }, $csvArray);

        return $csv;
    }

}
