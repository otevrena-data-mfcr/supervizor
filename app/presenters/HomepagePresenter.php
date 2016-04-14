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


class HomepagePresenter extends BasePresenter
{
    /** @var string @persistent */
    public $budgetGroupIdentifier = null;

    /** @var int @persistent */
    public $page = 1;

    /** @var string @persistent */
    public $supplierIdentifier = null;


    /**
     * @param null $budgetGroupIdentifier
     * @param int $page
     * @param null $supplierIdentifier
     */
    public function renderDefault()
    {
        $this->template->budgetGroupIdentifier = $this->budgetGroupIdentifier;
        $this->template->page = $this->page;
        $this->template->supplierIdentifier = $this->supplierIdentifier;
        $this->template->view = ($this->budgetGroupIdentifier ? 'skupina' : 'index');
        $this->template->title = 'Supervizor Ministerstva financí';
    }

    /**
     * @param bool $popup
     */
    public function renderAbout($popup = false)
    {
        if ($popup)
        {
            $this->setLayout(false);
        }
        $this->template->popup = $popup;
        $this->template->title = 'O projektu';
    }

}
