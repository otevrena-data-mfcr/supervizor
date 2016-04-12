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

namespace Extensions\Importer;

use Nette;

class Importer extends Nette\Object
{

    /** @var string */
    public static $namespace = 'Importer-Importer';

    /** @var Nette\Caching\Cache */
    public $cache;

    /** @var array */
    private $imports = [];

    /** @var  IImportTarget */
    private $target;

    /**
     * Importer constructor.
     * @param Nette\Caching\IStorage $cacheStorage
     * @param array $imports
     * @param $target
     */
    public function __construct(Nette\Caching\IStorage $cacheStorage, array $imports, $target)
    {
        $this->cache = new Nette\Caching\Cache($cacheStorage, self::$namespace);
        $this->imports = $imports;
        $this->target = $target;
    }

    /**
     *
     */
    public function doImport()
    {
        foreach ($this->imports as $import)
        {
            foreach ($import['datasets'] AS $dataset)
            {
                //Parse
                new $dataset['parser']($this->cache, $dataset['source'], $this->target);
            }
        }
    }

}
