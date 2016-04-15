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

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Kdyby\Doctrine\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DefaultData extends Command
{

    /** @var EntityManager @inject */
    public $em;

    protected function configure()
    {
        $this
                ->setName('orm:default-data:load')
                ->setDescription('Load data fixtures to your database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try
        {
            $loader = new Loader();
            $loader->loadFromDirectory(__DIR__ . '/DefaultData/');
            $fixtures = $loader->getFixtures();

            $purger = new ORMPurger($this->em);

            $executor = new ORMExecutor($this->em, $purger);
            $executor->setLogger(function ($message) use ($output)
            {
                $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
            });
            $executor->execute($fixtures);
            return 0; // zero return code means everything is ok
        }
        catch (\Exception $exc)
        {
            $output->writeln("<error>{$exc->getMessage()}</error>");
            return 1; // non-zero return code means error
        }
    }

}
