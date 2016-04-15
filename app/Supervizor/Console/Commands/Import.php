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

namespace Supervizor\Console\Commands;

use Kdyby\Doctrine\EntityManager;
use Nette\Utils\Json;
use Supervizor\Budget\BudgetGroup;
use Supervizor\Budget\BudgetItem;
use Supervizor\DI\Importer\Importer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Import extends Command
{

    /** @var Importer @inject */
    public $importer;

    /** @var EntityManager @inject */
    public $entityManager;
    
    protected function configure()
    {
        $this->setName('importer:import:all')
            ->setDescription('Imports all configured sources.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->importer->doImport();

            $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', 'Import was successful'));

            $budgetGroups = Json::decode(file_get_contents(__DIR__ . '/budget.groups.json'));

            $budgetItemEntityManager = $this->entityManager->getRepository(BudgetItem::class);
            $budgetGroupEntityManager = $this->entityManager->getRepository(BudgetGroup::class);

            foreach ($budgetGroups AS $budgetGroupSrc) {
                $foundGroup = $budgetGroupEntityManager->findBy(['name' => $budgetGroupSrc['name']]);
                
                if ($foundGroup) {
                    $budgetGroup = $foundGroup;
                } else {
                    $budgetGroup = new BudgetGroup(
                        $budgetGroupSrc['name'],
                        $budgetGroupSrc['key'],
                        $budgetGroupSrc['description'],
                        $budgetGroupSrc['x'],
                        $budgetGroupSrc['y'],
                        $budgetGroupSrc['color']
                    );

                    $this->entityManager->persist($budgetGroup);
                }

                /** @var BudgetItem $budgetItem */
                foreach ($budgetItemEntityManager->findBy(['identifier' => $budgetGroupSrc['items']]) AS $budgetItem) {
                    $budgetItem->setBudgetGroup($budgetGroup);
                }
            }

            $this->entityManager->flush();
            $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', 'Grouping imported data went ok!'));
        } catch (\Exception $exc) {
            $output->writeln("<error>{$exc->getMessage()}</error>");
            return 1; // non-zero return code means error
        }
        
        return 0; // zero return code means everything is ok
    }

}
