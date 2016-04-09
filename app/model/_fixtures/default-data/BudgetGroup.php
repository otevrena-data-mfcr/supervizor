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

namespace App\Model\Fixtures\DefaultData;

use App\Model\Entities\BudgetGroup;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class BudgetGroupFixtures extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * BudgetGroupFixtures constructor.
     */
    public function __construct()
    {
    }
    
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {        
        $budgetGroups = [];
        $budgetGroups[] = [
            'name' => 'Poradenské služby',
            'description' => 'Konzultační služby a školení',
            'x' => 250,
            'y' => 150,
            'color' => '2db9ff'
        ];
        
        $budgetGroups[] = [
            'name' => 'Doprava',
            'description' => 'Výdaje na cestování a nákup a provoz vozidel',
            'x' => 550,
            'y' => 300,
            'color' => 'fe8f3c'
        ];
        
        $budgetGroups[] = [
            'name' => 'Informační technologie',
            'description' => 'Hardware, software a telekomunikace',
            'x' => 700,
            'y' => 120,
            'color' => '66a22a'
        ];
        
        $budgetGroups[] = [
            'name' => 'Nákup majetku',
            'description' => '',
            'x' => 300,
            'y' => 700,
            'color' => '093d93'
        ];
        
        $budgetGroups[] = [
            'name' => 'Ostatní',
            'description' => 'Vše nezahrnuté v ostatních kategoriích',
            'x' => 1000,
            'y' => 700,
            'color' => '5a5a5a'
        ];
        
        $budgetGroups[] = [
            'name' => 'Výdaje na zaměstnance',
            'description' => 'Platy, odměny, závodní stravování a další výdaje na zaměstnance',
            'x' => 200,
            'y' => 450,
            'color' => 'eedc00'
        ];
        
        $budgetGroups[] = [
            'name' => 'Provozní náklady',
            'description' => 'Náklady na provoz ministerstva financí',
            'x' => 800,
            'y' => 500,
            'color' => 'c00000'
        ];

        foreach ($budgetGroups AS $budgetGroupSrc)
        {
            $budgetGrouo = new BudgetGroup($budgetGroupSrc['name'], $budgetGroupSrc['description'], $budgetGroupSrc['x'], $budgetGroupSrc['y'], $budgetGroupSrc['color']);
                        
            $manager->persist($budgetGrouo);
        }

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }

}
