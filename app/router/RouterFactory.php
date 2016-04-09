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

use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;

/**
 * Router factory.
 */
class RouterFactory
{

    /**
     * @return Nette\Application\IRouter
     */
    public function createRouter()
    {
        $useSsl = (
                isset($_SERVER['REMOTE_ADDR']) &&
                !in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1')) ? true : false);

        $router = new RouteList();

        $router[] = new Route('index.php', 'Homepage:default', Route::ONE_WAY);
        $router[] = new Route('faktura/<id>', 'Invoice:default', ($useSsl ? Route::SECURED : null));
        $router[] = new Route('skupina/<budgetGroupIdentifier>[/<page>]', 'Homepage:default', ($useSsl ? Route::SECURED : null));
        $router[] = new Route('skupina/<budgetGroupIdentifier>[/<page>]<supplierIdentifier>', 'Homepage:default', ($useSsl ? Route::SECURED : null));
        $router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default', ($useSsl ? Route::SECURED : null));

        return $router;
    }

}
