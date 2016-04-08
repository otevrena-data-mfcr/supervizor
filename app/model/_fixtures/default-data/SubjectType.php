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

use App\Model\Entities\SubjectType;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class SubjectTypeFixtures extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * CountriesFixtures constructor.
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
        $subjectTypes = [];
        $subjectTypes[] = [
            'name' => 'Fyzická osoba',
            'default' => true,
            'requiresSubjectName' => false
        ];
        
        $subjectTypes[] = [
            'name' => 'Právnická osoba',
            'default' => false,
            'requiresSubjectName' => true
        ];
        
        foreach ($subjectTypes AS $subjectTypeSrc)
        {
            $subjectType = new SubjectType($subjectTypeSrc['name'], $subjectTypeSrc['default'], $subjectTypeSrc['requiresSubjectName']);
            $manager->persist($subjectType);
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
        return 2;
    }

}

