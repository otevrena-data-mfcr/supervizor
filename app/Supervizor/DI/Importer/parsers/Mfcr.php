<?php

namespace Supervizor\DI\Importer\Parsers;

use Nette;

class Mfcr extends ImportParser implements IImportParser
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
     * Mfcr constructor.
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
     * @throws \Exception
     * @throws \Throwable
     */
    public function proccess()
    {

        $parsedDataKey = $this->info->id . '_parsedData';
        $lastModifiedKey = $this->info->id . '_lastModified';

        $lastModified = $this->cache->load($lastModifiedKey);
        if (!$lastModified || new \DateTime($lastModified) < new \DateTime($this->info->last_modified))
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
     * 
     * @param string $cs
     * @return float
     */
    private function cs2float($cs)
    {
        return (float) strtr($cs, array(',' => '.', ' ' => ''));
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
            $row = str_getcsv($rowRaw, ';');

            $inoviceUuid = ['MF'];
            switch ($row[0])
            {
                case "Přijaté faktury":
                    $inoviceUuid[] = "PF";
                    break;
                case "Ostatní platby":
                    $inoviceUuid[] = "OP";
                    break;
                default:
                    $inoviceUuid[] = "XX";
                    break;
            }
            $inoviceUuid[] = $row[1];

            //!FIXME migrate to constants, remove string sfrom DB
            switch ($row[6])
            {
                case 'faktura':
                    $type = 'Faktura';
                    break;
                case 'záloha':
                    $type = 'Zálohová faktura';
                    break;

                default:
                case 'ostatní':
                    $type = 'Ostatní platba';
                    break;
            }

            return [
                'identifier' => join("-", $inoviceUuid),
                'type' => $type,
                'distinction' => $row[0] ? : null,
                'vat_record' => $row[7] == "Ano",
                'ammount' => $row[9] ? $this->cs2float($row[9]) : null,
                'amountWithoutVat' => $row[10] ? $this->cs2float($row[10]) : null,
                'amountOriginal' => $row[11] ? $this->cs2float($row[11]) : null,
                'amountPaid' => $row[19] ? $this->cs2float($row[19]) : null,
                'amountPaidOriginal' => $row[20] ? $this->cs2float($row[20]) : null,
                'currency' => $row[13] ? : null,
                'issued' => $row[14] ? new \DateTime($row[14]) : null,
                'received' => $row[15] ? new \DateTime($row[15]) : null,
                'maturity' => $row[16] ? new \DateTime($row[16]) : null,
                'paid' => $row[17] ? new \DateTime($row[17]) : null,
                'description' => $row[18] ? : null,
                'supplierIdentifier' => $row[3] ? : null,
                'supplierName' => $row[2] ? : null,
                'supplierCompanyIdentifier' => $row[4] ? str_pad($row[4], 8, "0", STR_PAD_LEFT) : null,
                'budgetItemIdentifier' => ((int) $row[21]) ? : null,
                'budgetItemName' => $row[22] ? : null,
                'budgetItemAmount' => $row[23] ? $this->cs2float($row[23]) : null,
                'importId' => $this->import
            ];
        }, $csvArray);

        return $csv;
    }

}
