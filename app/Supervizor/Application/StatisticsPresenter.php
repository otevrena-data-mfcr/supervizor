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

use Supervizor\Statistics\StatisticsFacade;

/**
 * Class StatisticsPresenter
 */
class StatisticsPresenter extends Presenter
{

    /** @var StatisticsFacade @inject */
    public $statisticsFacade;



    public function renderLoadData()
    {
        list($skupiny_pocet, $skupiny_objem, $polozky_objem) = $this->statisticsFacade->loadStatistics();

        $this->payload->skupiny_pocet = $skupiny_pocet;
        $this->payload->skupiny_objem = $skupiny_objem;
        $this->payload->polozky_objem = $polozky_objem;

        $this->sendPayload();
    }



    public function renderDefault()
    {
        $this->template->title = 'Statistiky';
    }
}
