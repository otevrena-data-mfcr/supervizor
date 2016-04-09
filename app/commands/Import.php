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

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Extensions\Importer\Importer;
use Kdyby\Doctrine\EntityManager;
use App\Model\Entities\BudgetGroup;
use App\Model\Entities\BudgetItem;

class Import extends Command
{
    /** @var Importer @inject */
    public $importer;
    
    /** @var EntityManager @inject */
    public $entityManager;

    protected function configure()
    {
        $this
            ->setName('importer:import:all')
            ->setDescription('Imports all configured sources.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->importer->doImport();
           
            $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', 'Import was successful'));
            
            $budgetGroups = [];
            $budgetGroups[] = [
                'name' => 'Poradenské služby',
                'key' => 'consulting',
                'description' => 'Konzultační služby a školení',
                'x' => 250,
                'y' => 150,
                'color' => '2db9ff',
                'items' => 
                [
                    5166,
                    5167
                ]
            ];

            $budgetGroups[] = [
                'name' => 'Doprava',
                'key' => 'doprava',
                'description' => 'Výdaje na cestování a nákup a provoz vozidel',
                'x' => 550,
                'y' => 300,
                'color' => 'fe8f3c',
                'items' => 
                [
                    5156,
                    5173
                ]
            ];

            $budgetGroups[] = [
                'name' => 'Informační technologie',
                'key' => 'it',
                'description' => 'Hardware, software a telekomunikace',
                'x' => 700,
                'y' => 120,
                'color' => '66a22a',
                'items' => 
                [
                    5041,
                    5042,
                    5162,
                    5168,
                    5172,
                    6111,
                    6125
                ]
            ];

            $budgetGroups[] = [
                'name' => 'Nákup majetku',
                'key' => 'nakup',
                'description' => '',
                'x' => 300,
                'y' => 700,
                'color' => '093d93',
                'items' => 
                [
                    5137,
                    5139,
                    5177,
                    5179,
                    5199,
                    6122,
                    6123
                ]
            ];

            $budgetGroups[] = [
                'name' => 'Ostatní',
                'key' => 'ostatni',
                'description' => 'Vše nezahrnuté v ostatních kategoriích',
                'x' => 1000,
                'y' => 700,
                'color' => '5a5a5a',
                'items' => 
                [
                    1014,
                    2132,
                    2328,
                    5169,
                    5182,
                    5192,
                    5221,
                    5319,
                    5362,
                    5363,
                    5429,
                    5511,
                    5532,
                    5909
                ]
            ];

            $budgetGroups[] = [
                'name' => 'Výdaje na zaměstnance',
                'key' => 'platy',
                'description' => 'Platy, odměny, závodní stravování a další výdaje na zaměstnance',
                'x' => 200,
                'y' => 450,
                'color' => 'eedc00',
                'items' => 
                [
                    1012,
                    1013,
                    1017,
                    1018,
                    5011,
                    5013,
                    5021,
                    5024,
                    5031,
                    5032,
                    5342,
                    5422,
                    5424
                ]
            ];

            $budgetGroups[] = [
                'name' => 'Provozní náklady',
                'key' => 'provoz',
                'description' => 'Náklady na provoz ministerstva financí',
                'x' => 800,
                'y' => 500,
                'color' => 'c00000',
                'items' => [
                    1016,
                    1022,
                    1361,
                    2324,
                    5131,
                    5134,
                    5136,
                    5142,
                    5151,
                    5152,
                    5153,
                    5154,
                    5157,
                    5161,
                    5163,
                    5164,
                    5171,
                    5175,
                    5176,
                    5194,
                    6121
                ]
            ];

            $budgetItemEntityManager = $this->entityManager->getRepository(BudgetItem::class);
            $budgetGroupEntityManager = $this->entityManager->getRepository(BudgetGroup::class);
            foreach ($budgetGroups AS $budgetGroupSrc)
            {
                $foundGroup = $budgetGroupEntityManager->findBy(['name' => $budgetGroupSrc['name']]);
                if ($foundGroup)
                {
                    $budgetGroup = $foundGroup;
                }
                else
                {
                    $budgetGroup = new BudgetGroup($budgetGroupSrc['name'], $budgetGroupSrc['key'], $budgetGroupSrc['description'], $budgetGroupSrc['x'], $budgetGroupSrc['y'], $budgetGroupSrc['color']);

                    $this->entityManager->persist($budgetGroup);
                }
                
                foreach($budgetItemEntityManager->findBy(['identifier' => $budgetGroupSrc['items']]) AS $budgetItem)
                {
                    $budgetItem->setBudgetGroup($budgetGroup);
                }
            }

            $this->entityManager->flush();
            
            $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', 'Grouping imported data went ok!'));
            
            return 0; // zero return code means everything is ok
        } catch (\Exception $exc) {
            $output->writeLn("<error>{$exc->getMessage()}</error>");
            return 1; // non-zero return code means error
        }
    }
}