<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Extensions\Importer\Parsers;

use Nette;

/**
 * Description of IImportMapper
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
interface IImportParser
{

    /**
     * IImportParser constructor.
     * @param Nette\Caching\Cache $cache
     * @param $data
     * @param $target
     * @param $importId
     */
    public function __construct(Nette\Caching\Cache $cache, $data, $target, $import);
}
